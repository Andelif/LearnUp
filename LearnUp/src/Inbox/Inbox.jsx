import React, { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./Inbox.css";
import { toast } from "react-toastify";

const Inbox = () => {
  const { user, token } = useContext(storeContext);
  const [matchedUsers, setMatchedUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [showConfirmForm, setShowConfirmForm] = useState(false);
  const [finalizedSalary, setFinalizedSalary] = useState("");
  const [finalizedDays, setFinalizedDays] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";
  const navigate=useNavigate();


  useEffect(() => {
    const fetchMatchedUsers = async () => {
      try {
        const response = await axios.get(`${apiBaseUrl}/api/matched-users`, {
          headers: { Authorization: `Bearer ${token}` },
          withCredentials: true,
        });

        if (response.status === 200 && Array.isArray(response.data)) {
          setMatchedUsers(response.data);
          console.log(response.data);
        }
      } catch (err) {
        console.error("Error fetching matched users:", err);
      }
    };

    fetchMatchedUsers();
  }, []);

  useEffect(() => {
    if (selectedUser) {
      fetchMessages(selectedUser.user_id);
    }
  }, [selectedUser]);

  const fetchMessages = async (userId) => {
    try {
      const response = await axios.get(`${apiBaseUrl}/api/messages/${userId}`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setMessages(response.data);
    } catch (err) {
      console.error("Error fetching messages:", err);
    }
  };

  const sendMessage = async () => {
    if (!newMessage.trim() || !selectedUser) return;
    try {
      await axios.post(
        `${apiBaseUrl}/api/messages`,
        { SentTo: selectedUser.user_id, Content: newMessage },
        { headers: { Authorization: `Bearer ${token}` }, withCredentials: true }
      );
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
    

    //console.log("Selected Users: ", selectedUser);
    //console.log("Matched Users: ", matchedUsers);

    try {

      
      const response = await axios.post(
        `${apiBaseUrl}/api/confirmed-tuitions`,
        {
          application_id: selectedUser.ApplicationID,
          tution_id: selectedUser.tution_id,
          FinalizedSalary: finalizedSalary,
          FinalizedDays: finalizedDays,
          Status: "Ongoing",
        },
        { headers: { Authorization: `Bearer ${token}` }, withCredentials: true }
      );
      console.log("API Response:", response);

      if (response.status === 201) {
        toast.success("Tutor confirmed successfully", { autoClose: 2000 });
<<<<<<< Updated upstream
        navigate("/dashboard", { state: { finalizedSalary, selectedUser } });
        setShowConfirmForm(false);
        setSelectedUser(null);
      }
    } catch (err) {
      if (err.response?.status === 400) {
        toast.error("You have already confirmed this tutor", { autoClose: 2000 });
      } else {
        toast.error("Failed to confirm tutor.", { autoClose: 2000 });
      }
    }

=======
        setShowConfirmForm(false);
        setSelectedUser(null);
      }


    } catch (err) {
      if(err.response && err.response.status === 512){
        toast.error("You have already confirmed this tutor", { autoClose: 2000 });

      }else{

      console.error("Error confirming tutor:", err);
      alert("Failed to confirm tutor.");


    }

    }

>>>>>>> Stashed changes

  };

  return (
    <div className="inbox-container">

      
      <div className="inbox-sidebar">
        <h3>Chats</h3>
        <ul>
          {matchedUsers.length === 0 ? (
            <p>No matched users.</p>
          ) : (
            matchedUsers.map((matchedUser) => (
              <li key={matchedUser.user_id} className="ChatList" onClick={() => setSelectedUser(matchedUser)}>
                {matchedUser.full_name} Tuition ID: {matchedUser.tution_id}
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
                <div key={msg.MessageID} className={`message ${msg.SentBy === user.id ? "sent" : "received"}`}>
                  <p className="MsgContent">{msg.Content}</p>
                  <span>{new Date(msg.TimeStamp).toLocaleTimeString()}</span>
                </div>
              ))}
            </div>
            <div className="inbox-input">
              <input type="text" value={newMessage} onChange={(e) => setNewMessage(e.target.value)} placeholder="Type a message..." />
              <button onClick={sendMessage}>Send</button>
            </div>

            {user?.role === "learner" && (
              <div className="action-buttons">
                <button className="confirm-btn" onClick={() => setShowConfirmForm(true)}>Confirm Tutor</button>
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
                    <input type="number" value={finalizedSalary} onChange={(e) => setFinalizedSalary(e.target.value)} required />
                  </label>
                  <label>
                    Finalized Days:
                    <input type="text" value={finalizedDays} onChange={(e) => setFinalizedDays(e.target.value)} required />
                  </label>
                  <button type="submit">Confirm</button>
                  <button type="button" onClick={() => setShowConfirmForm(false)}>Cancel</button>
                </form>
              </div>
            )}

    </div>
  );
};

export default Inbox;
