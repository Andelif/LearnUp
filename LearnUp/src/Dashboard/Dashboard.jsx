import "./Dashboard.css";
import { useContext, useState, useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";
import { Link } from "react-router-dom";

const Dashboard = () => {
  const { api, user } = useContext(storeContext);

  const [error, setError] = useState("");
  const [stats, setStats] = useState({
    appliedJobs: 0,
    shortlistedJobs: 0,
    confirmedJobs: 0,
    cancelledJobs: 0,
  });
  const [matchedUsers, setMatchedUsers] = useState([]);
  const [tuitionId, setTuitionId] = useState(null);

  useEffect(() => {
    if (user?.id && user?.role) {
      fetchStats();
      fetchMatchedUsers();
      fetchTuitionDetails();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id, user?.role, api]);

  const fetchTuitionDetails = async () => {
    try {
      const { data } = await api.get(`/api/confirmed-tuitions`);
      if (Array.isArray(data) && data[0]?.tution_id) {
        setTuitionId(data[0].tution_id);
      } else {
        setTuitionId(null);
      }
    } catch (err) {
      console.error("Error fetching tuition details:", err);
      setTuitionId(null);
    }
  };

  const fetchStats = async () => {
    try {
      const { data } = await api.get(`/api/dashboard/${user.id}/${user.role}`);
      if (data && typeof data === "object") {
        setStats(data);
        setError("");
      } else {
        throw new Error("Invalid API response");
      }
    } catch (err) {
      console.error("Failed to fetch dashboard stats:", err);
      setError("Failed to load statistics. Please try again.");
    }
  };

  const fetchMatchedUsers = async () => {
    try {
      const { data } = await api.get(`/api/matched-users`);
      setMatchedUsers(Array.isArray(data) ? data : []);
      setError("");
    } catch (err) {
      console.error("Error fetching matched users:", err);
      setError("Failed to load matched users.");
      setMatchedUsers([]);
    }
  };

  return (
    <div className="dashboard-container">
      <aside className="sidebar">
        <div className="profile-section">
          <Link to="/ProfilePage">
            <img src="https://via.placeholder.com/80" alt="Profile" className="profile-pic" />
          </Link>
          <h3>{user?.name}</h3>
          <p>{user?.email}</p>
        </div>

        <Link to="/jobBoard" className="sidebar-link">Job Board</Link>
        {user?.role === "learner" && (
          <Link to="/myTuitions" className="sidebar-link">My Tuitions</Link>
        )}

        {user?.role === "tutor" && tuitionId && (
          <Link to={`/voucher/${tuitionId}`} className="btn btn-primary">
            View Payment Voucher
          </Link>
        )}

        <div>
          <Link to="/inbox" className="sidebar-link">Chat</Link>
        </div>
      </aside>

      <main className="dashboard-main">
        {error && <p className="error">{error}</p>}

        <div className="stats-container">
          <div className="stat-box">
            <h2>{user?.role === "tutor" ? stats.appliedJobs : stats.appliedRequests}</h2>
            <p>{user?.role === "tutor" ? "Applied Jobs" : "Applied Requests"}</p>
          </div>

          <div className="stat-box">
            <h2>{user?.role === "tutor" ? stats.shortlistedJobs : stats.shortlistedTutors}</h2>
            <p>{user?.role === "tutor" ? "Shortlisted Jobs" : "Shortlisted Tutors"}</p>
          </div>

          <div className="stat-box">
            <h2>{user?.role === "tutor" ? stats.confirmedJobs : stats.confirmedTutors}</h2>
            <p>{user?.role === "tutor" ? "Confirmed Jobs" : "Confirmed Tutors"}</p>
          </div>

          <div className="stat-box">
            <h2>{user?.role === "tutor" ? stats.cancelledJobs : stats.cancelledTutors}</h2>
            <p>{user?.role === "tutor" ? "Cancelled Jobs" : "Cancelled Tutors"}</p>
          </div>
        </div>

        <div className="notice-board">
          <h3>Notice Board</h3>
          <p>"Tutor of the Month, December 2024" is Md. Abidur Rahman...</p>
        </div>

        <div className="info-boxes">
          <div className="info-box">
            <FaCalendarAlt /> <p>Member Since: Aug 21, 2022</p>
          </div>
          <div className="info-box">
            <FaEnvelope /> <p>03 Confirmation Letters</p>
          </div>
          <div className="info-box">
            <FaFileInvoice /> <p>01 Invoice Pending</p>
          </div>
        </div>
      </main>
    </div>
  );
};

export default Dashboard;
