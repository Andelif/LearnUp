import React, { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import "./Inbox.css";
import { toast } from "react-toastify";

const Inbox = () => {
  const { api, user } = useContext(storeContext);

  const [matchedUsers, setMatchedUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [showConfirmForm, setShowConfirmForm] = useState(false);
  const [finalizedSalary, setFinalizedSalary] = useState("");
  const [finalizedDays, setFinalizedDays] = useState("");

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get("/api/matched-users");
        setMatchedUsers(Array.isArray(data) ? data : []);
      } catch (err) {
        console.error("Error fetching matched users:", err);
      }
    })();
  }, [api]);

  useEffect(() => {
    if (!selectedUser) return;
    fetchMessages(selectedUser.user_id);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedUser]);

  const fetchMessages = async (userId) => {
    try {
      const { data } = await api.get(`/api/messages/${userId}`);
      setMessages(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Error fetching messages:", err);
    }
  };

  const sendMessage = async () => {
    if (!newMessage.trim() || !selectedUser) return;
    try {
      await api.post("/api/messages", {
        SentTo: selectedUser.user_id, // this is always a users.id
        Content: newMessage,
      });
      setNewMessage("");
      fetchMessages(selectedUser.user_id);
    } catch (err) {
      console.error("Error sending message", err);
    }
  };

  const confirmTutor = async (e) => {
    e.preventDefault();
    if (!finalizedSalary || !finalizedDays) {
      alert("Please fill all fields.");
      return;
    }
    try {
      const res = await api.post("/api/confirmed-tuitions", {
        application_id: selectedUser.ApplicationID,
        tution_id: selectedUser.tution_id, // keep spelling to match backend
        FinalizedSalary: finalizedSalary,
        FinalizedDays: finalizedDays,
        Status: "Ongoing",
      });

      if (res.status === 201) {
        toast.success("Tutor confirmed successfully", { autoClose: 2000 });
        setShowConfirmForm(false);
        setSelectedUser(null);
      }
    } catch (err) {
      if (err.response && err.response.status === 512) {
        toast.error("You have already confirmed this tutor", { autoClose: 2000 });
      } else {
        console.error("Error confirming tutor:", err);
        alert("Failed to confirm tutor.");
      }
    }
  };

  const rejectTutor = async () => {
    if (!selectedUser) return;
    try {
      // IMPORTANT: use TutorID (we expose it as tutor_id from /api/matched-users)
      await api.post("/api/reject-tutor", {
        tutor_id: selectedUser.tutor_id, // <-- fixed
        tution_id: selectedUser.tution_id,
      });
      alert("Tutor rejected successfully.");
      setSelectedUser(null);
    } catch (err) {
      console.error("Error rejecting tutor:", err);
      alert("Failed to reject tutor.");
    }
  };

  return (
    <div className="inbox-container">
      <div className="inbox-sidebar">
        <h3>Chats</h3>
        <ul>
          {matchedUsers.length === 0 ? (
            <p>No matched users.</p>
          ) : (
            matchedUsers.map((m) => (
              <li
                key={m.ApplicationID || `${m.user_id}-${m.tution_id}`} // more unique
                className="ChatList"
                onClick={() => setSelectedUser(m)}
              >
                {m.full_name} Tuition ID: {m.tution_id}
              </li>
            ))
          )}
        </ul>
      </div>

      <div className="inbox-chat">
        {selectedUser ? (
          <>
            <div className="inbox-header">
              <h3>Chat with {selectedUser.full_name}</h3>
              <button onClick={() => setSelectedUser(null)}>✖</button>
            </div>

            <div className="inbox-messages">
              {messages.map((msg) => (
                <div
                  key={msg.MessageID}
                  className={`message ${msg.SentBy === user?.id ? "sent" : "received"}`}
                >
                  <p className="MsgContent">{msg.Content}</p>
                  <span>{new Date(msg.TimeStamp).toLocaleTimeString()}</span>
                </div>
              ))}
            </div>

            <div className="inbox-input">
              <input
                type="text"
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                placeholder="Type a message..."
              />
              <button onClick={sendMessage}>Send</button>
            </div>

            {user?.role === "learner" && (
              <div className="action-buttons">
                <button className="confirm-btn" onClick={() => setShowConfirmForm(true)}>
                  Confirm Tutor
                </button>
                <button className="reject-btn" onClick={rejectTutor}>
                  Reject Tutor
                </button>
              </div>
            )}
          </>
        ) : (
          <></>
        )}
      </div>

      {showConfirmForm && (
        <div className="confirm-form">
          <h3>Confirm Tutor</h3>
          <form onSubmit={confirmTutor}>
            <label>
              Finalized Salary:
              <input
                type="number"
                value={finalizedSalary}
                onChange={(e) => setFinalizedSalary(e.target.value)}
                required
              />
            </label>
            <label>
              Finalized Days:
              <input
                type="text"
                value={finalizedDays}
                onChange={(e) => setFinalizedDays(e.target.value)}
                required
              />
            </label>
            <button type="submit">Confirm</button>
            <button type="button" onClick={() => setShowConfirmForm(false)}>
              Cancel
            </button>
          </form>
        </div>
      )}
    </div>
  );
};

export default Inbox;
