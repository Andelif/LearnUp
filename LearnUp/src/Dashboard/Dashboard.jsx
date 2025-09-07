import "./Dashboard.css";
import { useContext, useState, useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";
import { Link } from "react-router-dom";

const Dashboard = () => {
  const { api, user, token } = useContext(storeContext);

  const [error, setError] = useState("");
  const [stats, setStats] = useState({
    appliedJobs: 0,
    shortlistedJobs: 0,
    confirmedJobs: 0,
    cancelledJobs: 0,
    // learner keys (in case backend returns a different shape)
    appliedRequests: 0,
    shortlistedTutors: 0,
    confirmedTutors: 0,
    cancelledTutors: 0,
  });
  const [matchedUsers, setMatchedUsers] = useState([]);
  const [tuitionId, setTuitionId] = useState(null);

  useEffect(() => {
    if (user?.id && user?.role && token) {
      fetchStats();
      fetchMatchedUsers();
      fetchTuitionDetails();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id, user?.role, token]);

  const authHeader = {
    headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
  };

  const fetchTuitionDetails = async () => {
    try {
      const { data } = await api.get(`/api/confirmed-tuitions`, authHeader);
      const first = Array.isArray(data) ? data[0] : null;
      // tolerate tution_id / tuition_id
      setTuitionId(first?.tution_id ?? first?.tuition_id ?? null);
    } catch (err) {
      console.error("Error fetching tuition details:", err);
      setTuitionId(null);
    }
  };

  const fetchStats = async () => {
    try {
      const { data } = await api.get(
        `/api/dashboard/${user.id}/${user.role}`,
        authHeader
      );
      if (data && typeof data === "object") {
        setStats((prev) => ({ ...prev, ...data }));
        setError("");
      } else {
        throw new Error("Invalid API response");
      }
    } catch (err) {
      console.error("Failed to fetch dashboard stats:", err);
      const msg =
        err?.response?.data?.message ||
        err?.message ||
        "Failed to load statistics.";
      setError(msg);
    }
  };

  const fetchMatchedUsers = async () => {
    try {
      const { data } = await api.get(`/api/matched-users`, authHeader);
      setMatchedUsers(Array.isArray(data) ? data : []);
    } catch (err) {
      console.error("Error fetching matched users:", err);
      setMatchedUsers([]);
    }
  };

  return (
    <div className="dashboard-container">
      <aside className="sidebar">
        <div className="profile-sections">
          <Link to="/ProfilePage">
            <img
              src="https://via.placeholder.com/80"
              alt="Profile"
              className="profile-pic"
            />
          </Link>
          <h3>{user?.name}</h3>
          <p>{user?.email}</p>
        </div>

        <Link to="/jobBoard" className="sidebar-link">
          Job Board
        </Link>

        {user?.role === "learner" && (
          <Link to="/myTuitions" className="sidebar-link">
            My Tuitions
          </Link>
        )}

        {user?.role === "tutor" && tuitionId && (
          <Link to={`/voucher/${tuitionId}`} className="btn btn-primary">
            View Payment Voucher
          </Link>
        )}

        <div>
          <Link to="/inbox" className="sidebar-link">
            Chat
          </Link>
        </div>
      </aside>

      <main className="dashboard-main">
        {error && <p className="error">{error}</p>}

        <div className="stats-container">
          <div className="stat-box">
            <h2>
              {user?.role === "tutor"
                ? stats.appliedJobs
                : stats.appliedRequests}
            </h2>
            <p>{user?.role === "tutor" ? "Applied Jobs" : "Applied Requests"}</p>
          </div>

          <div className="stat-box">
            <h2>
              {user?.role === "tutor"
                ? stats.shortlistedJobs
                : stats.shortlistedTutors}
            </h2>
            <p>
              {user?.role === "tutor" ? "Shortlisted Jobs" : "Shortlisted Tutors"}
            </p>
          </div>

          <div className="stat-box">
            <h2>
              {user?.role === "tutor"
                ? stats.confirmedJobs
                : stats.confirmedTutors}
            </h2>
            <p>
              {user?.role === "tutor" ? "Confirmed Jobs" : "Confirmed Tutors"}
            </p>
          </div>

          <div className="stat-box">
            <h2>
              {user?.role === "tutor"
                ? stats.cancelledJobs
                : stats.cancelledTutors}
            </h2>
            <p>
              {user?.role === "tutor" ? "Cancelled Jobs" : "Cancelled Tutors"}
            </p>
          </div>
        </div>

        <div className="notice-board">
          <h3>Notice Board</h3>
          <p>"Tutor of the Month, December 2024" is Md. Abidur Rahman...</p>
        </div>

        <div className="info-boxes">
          <div className="info-box">
            <FaCalendarAlt /> <p> Member Since:{" "} {user?.created_at ? (() => {
          const [datePart] = user.created_at.split(" "); // "2025-03-08"
          const [year, month, day] = datePart.split("-");
          const dateObj = new Date(year, month - 1, day); // month - 1 because JS months are 0-indexed
          return dateObj.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
          });
        })(): "N/A"}</p>
          </div>
          <div className="info-box">
            <FaEnvelope /> <p>03 Confirmation Letters</p>
          </div>
          <div className="info-box">
            <FaFileInvoice /> <p>01 Invoice Pending</p>
          </div>
        </div>

        {/* You have matchedUsers state if you want to show a list later */}
      </main>
    </div>
  );
};

export default Dashboard;
