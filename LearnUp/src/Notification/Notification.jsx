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

  const canMarkRead = (n) =>
    n.Status === "Unread" &&
    n.user_id &&
    user &&
    Number(n.user_id) === Number(user.id);

  // Check if there are unread notifications for the dot
  const hasUnread = notifications.some((n) => n.Status === "Unread");

  return (
    <div className="notification-wrapper">
      {/* Notification Icon in Header/Navbar */}
      

      {/* Notification List */}
      <div className="notification-center">
        <h2>Notification Center</h2>
        {error && <p className="error-message">{error}</p>}

        {notifications.length === 0 ? (
          <p className="no-notifications">No notifications available.</p>
        ) : (
          <ul className="notification-list">
            {notifications.map((notif) => (
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
                    {notif.TimeSent
                      ? new Date(notif.TimeSent).toLocaleString()
                      : ""}
                  </p>

                  {notif.view && (
                    <p className="notification-badge">
                      {notif.view === "everyone"
                        ? "Broadcast to everyone"
                        : notif.view === "all_learner"
                        ? "Broadcast to learners"
                        : notif.view === "all_tutor"
                        ? "Broadcast to tutors"
                        : notif.view === "all_admin"
                        ? "Broadcast to admins"
                        : null}
                    </p>
                  )}
                </div>

                {canMarkRead(notif) && (
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
    </div>
  );
};

export default Notification;
