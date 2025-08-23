import React, { useEffect, useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import "./Notification.css";

const Notification = () => {
  const { api, user } = useContext(storeContext);
  const [notifications, setNotifications] = useState([]);
  const [error, setError] = useState("");

  useEffect(() => {
    fetchNotifications();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const fetchNotifications = async () => {
    try {
      const { data } = await api.get("/api/notifications");
      setNotifications(Array.isArray(data) ? data : []);
      setError("");
    } catch (err) {
      console.error("Error fetching notifications:", err);
      setError("Failed to fetch notifications.");
      setNotifications([]);
    }
  };

  const markAsRead = async (id) => {
    try {
      await api.put(`/api/notifications/${id}/read`, {});
      setNotifications((prev) =>
        prev.map((n) =>
          n.NotificationID === id ? { ...n, Status: "Read" } : n
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
          {notifications
            .filter((notif) => {
              // show to everyone / all learners / all tutors / specific user
              return (
                notif.view === "everyone" ||
                (notif.view === "all_learner" && user?.role === "learner") ||
                (notif.view === "all_tutor" && user?.role === "tutor") ||
                notif.view == user?.id
              );
            })
            .map((notif) => (
              <li
                key={notif.NotificationID}
                className={`notification-item ${
                  notif.Status === "Unread" ? "unread" : ""
                }`}
              >
                <div className="notification-content">
                  <p className="notification-type">{notif.Type}</p>
                  <p className="notification-message">{notif.Message}</p>
                  <p className="notification-time">
                    {new Date(notif.TimeSent).toLocaleString()}
                  </p>
                </div>
                {notif.Status === "Unread" && (
                  <button
                    className="mark-read-btn"
                    onClick={() => markAsRead(notif.NotificationID)}
                  >
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
