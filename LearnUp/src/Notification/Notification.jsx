import React, { useEffect, useState, useContext } from "react";
import axios from "axios";
import { storeContext } from "../context/contextProvider";
import "./Notification.css";

const Notification = () => {
  const { user, token } = useContext(storeContext);
  const [notifications, setNotifications] = useState([]); // Default to an empty array
  const [error, setError] = useState("");

  useEffect(() => {
    fetchNotifications();
  }, []);

  const fetchNotifications = async () => {
    try {
      const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/api/notifications`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });

      // Ensure data is an array
      if (Array.isArray(response.data)) {
        setNotifications(response.data);
      } else {
        setNotifications([]); // Default to an empty array
      }
    } catch (err) {
      console.error("Error fetching notifications:", err);
      setError("Failed to fetch notifications.");
      setNotifications([]); // Prevent `map()` error
    }
  };

  const markAsRead = async (id) => {
    try {
      await axios.put(`${import.meta.env.VITE_API_BASE_URL}/api/notifications/${id}/markAsRead`, {}, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });

      setNotifications((prev) =>
        prev.map((notif) =>
          notif.NotificationID === id ? { ...notif, Status: "Read" } : notif
        )
      );
    } catch (err) {
      console.error("Error marking notification as read:", err);
    }
  };

  return (
    <div className="notification-center">
      <h2>Notification Center</h2>
      {error && <p className="error-message">{error}</p>}

      {notifications.length === 0 ? (
        <p className="no-notifications">No notifications available.</p>
      ) : (
        <ul className="notification-list">
          {notifications.map((notif) => (
            <li key={notif.NotificationID} className={`notification-item ${notif.Status === "Unread" ? "unread" : ""}`}>
              <div className="notification-content">
                <p className="notification-type">{notif.Type}</p>
                <p className="notification-message">{notif.Message}</p>
                <p className="notification-time">{new Date(notif.TimeSent).toLocaleString()}</p>
              </div>
              {notif.Status === "Unread" && (
                <button className="mark-read-btn" onClick={() => markAsRead(notif.NotificationID)}>
                  Mark as Read
                </button>
              )}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default Notification;
