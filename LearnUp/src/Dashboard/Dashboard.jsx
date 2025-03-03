import "./Dashboard.css";
import { useContext, useState, useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";
import { Link } from "react-router-dom";
import axios from "axios";

const Dashboard = () => {
  const { user, token } = useContext(storeContext);
  const [apiBaseUrl, setApiBaseUrl] = useState(
    import.meta.env.VITE_API_BASE_URL || "http://localhost:8000"
  );
  const [error, setError] = useState("");
  const [stats, setStats] = useState({
    appliedJobs: 0,
    shortlistedJobs: 0,
  });
  const [matchedUsers, setMatchedUsers] = useState([]);
  


  useEffect(() => {
    if (user?.id && user?.role && apiBaseUrl) {
      fetchStats();
      fetchMatchedUsers();
    }
  }, [user, apiBaseUrl]);

  const fetchStats = async () => {
    if (!user?.id || !user?.role || !apiBaseUrl) return;

    try {
      console.log("Fetching stats from:", `${apiBaseUrl}/api/dashboard/${user.id}/${user.role}`);
      const response = await axios.get(`${apiBaseUrl}/api/dashboard/${user.id}/${user.role}`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });

      if (response.status !== 200 || typeof response.data !== "object") {
        throw new Error("Invalid API response");
      }

      console.log("Stats received:", response.data);
      setStats(response.data);
    } catch (err) {
      console.error("Failed to fetch dashboard stats:", err);
      setError("Failed to load statistics. Please try again.");
    }
  };

  const fetchMatchedUsers = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/api/matched-users`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
  
      if (response.status === 200 && Array.isArray(response.data)) {
        setMatchedUsers(response.data);
      } else {
        setMatchedUsers([]); // Fallback to an empty array if response is not an array
      }
    } catch (err) {
      console.error("Error fetching matched users:", err);
      setError("Failed to load matched users.");
      setMatchedUsers([]); // Ensure it's always an array
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
        {user?.role === "learner" && <Link to='/myTuitions' className="sidebar-link">My Tuitions</Link>}

       <div>
          <Link to="/inbox" className="sidebar-link">
            Chat
          </Link>
        </div> 

      </aside>

      <main className="dashboard-main">
        <div className="stats-container">
          <div className="stat-box"> 
            <h2>{user?.role === "tutor" ? stats.appliedJobs : stats.appliedRequests}</h2> 
            <p>{user?.role === "tutor" ? "Applied Jobs" : "Applied Requests"}</p> 
          </div>
          <div className="stat-box"> 
            <h2>{user?.role === "tutor" ? stats.shortlistedJobs : stats.shortlistedTutors}</h2> 
            <p>{user?.role === "tutor" ? "Shortlisted Jobs" : "Shortlisted Tutors"}</p> 
          </div>
          <div className="stat-box"> <h2>0</h2> <p>{user?.role === "tutor" ? "Appointed Jobs" : "Appointed Tutors"}</p> </div>
          <div className="stat-box"> <h2>0</h2> <p>{user?.role === "tutor" ? "Confirmed Jobs" : "Confirmed Tutors"}</p> </div>
          <div className="stat-box"> <h2>0</h2> <p>{user?.role === "tutor" ? "Cancelled Jobs" : "Cancelled Tutors"}</p> </div>
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
