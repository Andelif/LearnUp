import React, { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import axios from "axios";
import "./Inbox.css";

const Inbox = () => {
  const { user, token } = useContext(storeContext);
  const [matchedUsers, setMatchedUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

  // Fetch Matched Users
  useEffect(() => {
    const fetchMatchedUsers = async () => {
      try {
        const response = await axios.get(`${apiBaseUrl}/api/matched-users`, {
          headers: { Authorization: `Bearer ${token}` },
          withCredentials: true,
        });

        if (response.status === 200 && Array.isArray(response.data)) {
          setMatchedUsers(response.data);
        }
      } catch (err) {
        console.error("Error fetching matched users:", err);
      }
    };

    fetchMatchedUsers();
  }, []);

  // Fetch Messages when a user is selected
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

  return (
    <div className="inbox-container">
      {/* Sidebar with Matched Users */}
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

      {/* Chat Window */}
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
                  <p>{msg.Content}</p>
                  <span>{new Date(msg.TimeStamp).toLocaleTimeString()}</span>
                </div>
              ))}
            </div>
            <div className="inbox-input">
              <input type="text" value={newMessage} onChange={(e) => setNewMessage(e.target.value)} placeholder="Type a message..." />
              <button onClick={sendMessage}>Send</button>
            </div>
          </>
        ) : (
          <p>Select a user to chat.</p>
        )}
      </div>
    </div>
  );
};

export default Inbox;
