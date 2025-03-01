import React, { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import axios from "axios";
import "./Inbox.css";

const Inbox = ({ selectedUser, onClose }) => {
  const { user, token } = useContext(storeContext);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

  useEffect(() => {
    fetchMessages();
  }, [selectedUser]);

  const fetchMessages = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/api/messages/${selectedUser.id}`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setMessages(response.data);
    } catch (err) {
      console.error("Error fetching messages", err);
    }
  };

  const sendMessage = async () => {
    if (!newMessage.trim()) return;
    try {
      await axios.post(
        `${apiBaseUrl}/api/messages`,
        { SentTo: selectedUser.id, Content: newMessage },
        { headers: { Authorization: `Bearer ${token}` }, withCredentials: true }
      );
      setNewMessage("");
      fetchMessages();
    } catch (err) {
      console.error("Error sending message", err);
    }
  };

  return (
    <div className="inbox-overlay">
      <div className="inbox-container">
        <div className="inbox-header">
          {/* <h3>Chat with {selectedUser.name}</h3> */}
          <button onClick={onClose}>✖</button>
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
      </div>
    </div>
  );
};

export default Inbox;
