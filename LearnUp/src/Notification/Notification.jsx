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
      // 403 likely means it was a broadcast (cannot mark as read per-user)
      console.error("Error marking notification as read:", err);
    }
  };

  // helper: is this a personal notification we can mark as read?
  const canMarkRead = (n) =>
    n.Status === "Unread" && n.user_id && user && Number(n.user_id) === Number(user.id);

  return (
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
              className={`notification-item ${notif.Status === "Unread" ? "unread" : ""}`}
            >
              <div className="notification-content">
                <p className="notification-type">{notif.Type}</p>
                <p className="notification-message">{notif.Message}</p>
                <p className="notification-time">
                  {notif.TimeSent ? new Date(notif.TimeSent).toLocaleString() : ""}
                </p>

                {/* Optional: show who it's for */}
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
  );
};

export default Notification;
