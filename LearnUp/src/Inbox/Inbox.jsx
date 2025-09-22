import React, { useState, useEffect, useContext, useRef } from "react";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";
import "./Inbox.css";

const Inbox = () => {
  const { api, user } = useContext(storeContext);
  const messagesEndRef = useRef(null);

  const [matchedUsers, setMatchedUsers] = useState([]);
  const [selectedUser, setSelectedUser] = useState(null);
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState("");
  const [showConfirmForm, setShowConfirmForm] = useState(false);
  const [finalizedSalary, setFinalizedSalary] = useState("");
  const [finalizedDays, setFinalizedDays] = useState("");
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get("/api/matched-users");
        setMatchedUsers(Array.isArray(data) ? data : []);
      } catch (err) {
        console.error("Error fetching matched users:", err);
        toast.error("Failed to load conversations");
      } finally {
        setLoading(false);
      }
    })();
  }, [api]);

  useEffect(() => {
    if (!selectedUser) return;
    fetchMessages(selectedUser.user_id);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [selectedUser]);

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  const fetchMessages = async (userId) => {
    try {
      const { data } = await api.get(`/api/messages/${userId}`);
      setMessages(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Error fetching messages:", err);
      toast.error("Failed to load messages");
    }
  };

  const sendMessage = async () => {
    if (!newMessage.trim() || !selectedUser || sending) return;
    
    setSending(true);
    try {
      await api.post("/api/messages", {
        SentTo: selectedUser.user_id,
        Content: newMessage,
      });
      setNewMessage("");
      fetchMessages(selectedUser.user_id);
    } catch (err) {
      console.error("Error sending message", err);
      toast.error("Failed to send message");
    } finally {
      setSending(false);
    }
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  };

  const confirmTutor = async (e) => {
    e.preventDefault();
    if (!finalizedSalary || !finalizedDays) {
      toast.error("Please fill all fields");
      return;
    }
    try {
      const res = await api.post("/api/confirmed-tuitions", {
        application_id: selectedUser.ApplicationID,
        tution_id: selectedUser.tution_id,
        FinalizedSalary: finalizedSalary,
        FinalizedDays: finalizedDays,
        Status: "Ongoing",
      });

      if (res.status === 201) {
        toast.success("Tutor confirmed successfully");
        setShowConfirmForm(false);
        setSelectedUser(null);
        setFinalizedSalary("");
        setFinalizedDays("");
      }
    } catch (err) {
      if (err.response && err.response.status === 512) {
        toast.error("You have already confirmed this tutor");
      } else {
        console.error("Error confirming tutor:", err);
        toast.error("Failed to confirm tutor");
      }
    }
  };

  const rejectTutor = async () => {
    if (!selectedUser) return;
    
    if (!window.confirm("Are you sure you want to reject this tutor?")) return;
    
    try {
      await api.post("/api/reject-tutor", {
        tutor_id: selectedUser.tutor_id,
        tution_id: selectedUser.tution_id,
      });
      toast.success("Tutor rejected successfully");
      setSelectedUser(null);
    } catch (err) {
      console.error("Error rejecting tutor:", err);
      toast.error("Failed to reject tutor");
    }
  };

  const formatTime = (timestamp) => {
    const date = new Date(timestamp);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  };

  return (
    <div className="inbox-container">
      <div className="inbox-sidebar">
        <div className="sidebar-header">
          <h2>Messages</h2>
          <span className="badge">{matchedUsers.length}</span>
        </div>
        
        <div className="conversation-list">
          {loading ? (
            <div className="loading-conversations">
              <div className="spinner"></div>
              <p>Loading conversations...</p>
            </div>
          ) : matchedUsers.length === 0 ? (
            <div className="empty-conversations">
              <i className="fas fa-comments"></i>
              <p>No conversations yet</p>
            </div>
          ) : (
            matchedUsers.map((m) => (
              <div
                key={m.ApplicationID || `${m.user_id}-${m.tution_id}`}
                className={`conversation-item ${selectedUser?.user_id === m.user_id ? 'active' : ''}`}
                onClick={() => setSelectedUser(m)}
              >
                <div className="avatar">
                  {m.full_name ? m.full_name.charAt(0).toUpperCase() : 'U'}
                </div>
                <div className="conversation-info">
                  <h4>{m.full_name || 'Unknown User'}</h4>
                  <p className="tuition-info">Tuition #{m.tution_id}</p>
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      <div className="inbox-chat">
        {selectedUser ? (
          <>
            <div className="chat-header">
              <div className="chat-user-info">
                <div className="user-avatar">
                  {selectedUser.full_name ? selectedUser.full_name.charAt(0).toUpperCase() : 'U'}
                </div>
                <div>
                  <h3>{selectedUser.full_name}</h3>
                  <p className="user-status">Tuition #{selectedUser.tution_id}</p>
                </div>
              </div>
              <div className="chat-actions">
                <button 
                  className="close-chat"
                  onClick={() => setSelectedUser(null)}
                  title="Close chat"
                >
                  <i className="fas fa-times"></i>
                </button>
              </div>
            </div>

            <div className="messages-container">
              {messages.length === 0 ? (
                <div className="no-messages">
                  <i className="fas fa-comment-dots"></i>
                  <p>No messages yet. Start the conversation!</p>
                </div>
              ) : (
                messages.map((msg) => (
                  <div
                    key={msg.MessageID}
                    className={`message ${msg.SentBy === user?.id ? "sent" : "received"}`}
                  >
                    <div className="message-content">
                      <p>{msg.Content}</p>
                      <span className="message-time">{formatTime(msg.TimeStamp)}</span>
                    </div>
                  </div>
                ))
              )}
              <div ref={messagesEndRef} />
            </div>

            <div className="message-input-container">
              <div className="input-wrapper">
                <input
                  type="text"
                  value={newMessage}
                  onChange={(e) => setNewMessage(e.target.value)}
                  onKeyPress={handleKeyPress}
                  placeholder="Type a message..."
                  disabled={sending}
                />
                <button  onClick={sendMessage}  disabled={!newMessage.trim() || sending} className="send-button">
                  {sending ? (
                  <svg className="spinner" width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path d="M10 3.5C6.41015 3.5 3.5 6.41015 3.5 10C3.5 10.4142 3.16421 10.75 2.75 10.75C2.33579 10.75 2 10.4142 2 10C2 5.58172 5.58172 2 10 2C14.4183 2 18 5.58172 18 10C18 14.4183 14.4183 18 10 18C9.58579 18 9.25 17.6642 9.25 17.25C9.25 16.8358 9.58579 16.5 10 16.5C13.5899 16.5 16.5 13.5899 16.5 10C16.5 6.41015 13.5899 3.5 10 3.5Z" fill="white"/>
               </svg>
                 ) : (
               <svg className="paper-plane" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M1.5 5L18.5 10L1.5 15L1.5 5Z" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <path d="M1.5 5L9.5 10L1.5 15" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
                  )}
                </button>
              </div>
            </div>

            {user?.role === "learner" && (
              <div className="tutor-actions">
                <button 
                  className="btn-confirm"
                  onClick={() => setShowConfirmForm(true)}
                >
                  <i className="fas fa-check-circle"></i>
                  Confirm Tutor
                </button>
                <button 
                  className="btn-reject"
                  onClick={rejectTutor}
                >
                  <i className="fas fa-times-circle"></i>
                  Reject Tutor
                </button>
              </div>
            )}
          </>
        ) : (
          <div className="no-chat-selected">
            <div className="placeholder-icon">
              <i className="fas fa-comments"></i>
            </div>
            <h3>Select a conversation</h3>
            <p>Choose a conversation from the list to start messaging</p>
          </div>
        )}
      </div>

      {showConfirmForm && (
        <div className="modal-overlay">
          <div className="confirm-modal">
            <div className="modal-header">
              <h3>Confirm Tutor</h3>
              <button 
                className="close-modal"
                onClick={() => setShowConfirmForm(false)}
              >
                <i className="fas fa-times"></i>
              </button>
            </div>
            <form onSubmit={confirmTutor}>
              <div className="form-group">
                <label>Finalized Salary (BDT)</label>
                <input
                  type="number"
                  value={finalizedSalary}
                  onChange={(e) => setFinalizedSalary(e.target.value)}
                  placeholder="Enter amount"
                  required
                />
              </div>
              <div className="form-group">
                <label>Finalized Days</label>
                <input
                  type="text"
                  value={finalizedDays}
                  onChange={(e) => setFinalizedDays(e.target.value)}
                  placeholder="e.g., Monday, Wednesday, Friday"
                  required
                />
              </div>
              <div className="modal-actions">
                <button type="button" onClick={() => setShowConfirmForm(false)}>
                  Cancel
                </button>
                <button type="submit" className="btn-primary">
                  Confirm Tutor
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Inbox;