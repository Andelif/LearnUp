import "./Dashboard.css";
import { useContext, useState, useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice, FaEdit } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";
import { Link } from "react-router-dom";
import axios from "axios";

const Dashboard = () => {
  const { user, token } = useContext(storeContext);
  const [apiBaseUrl, setApiBaseUrl] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const [stats, setStats] = useState({
    appliedJobs: 0,
    shortlistedJobs: 0,
  });

  // Editable field names
  const fieldNames = {
    name: "Full Name",
    email: "Email Address",
    contact_number: "Contact Number",
    address: "Address",
    qualification: "Qualification",
    experience: "Experience",
    salary: "Preferred Salary",
    availability: "Availability",
  };

  useEffect(() => {
    setApiBaseUrl(import.meta.env.VITE_API_BASE_URL || "http://localhost:8000");
  }, []);


  useEffect(() => {
    if (user?.id && user?.role) {
      fetchStats();
    }
  }, [user]);


  const fetchStats = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/api/dashboard/${user.id}/${user.role}`, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setStats(response.data);
      console.log(response.data);
    } catch (err) {
      console.error("Failed to fetch dashboard stats", err);
    }
  };



  const fetchUserData = async () => {
    try {
      const endpoint = user.role === "tutor"
        ? `${apiBaseUrl}/api/tutors/${user.id}`
        : `${apiBaseUrl}/api/learners/${user.id}`;
      const response = await axios.get(endpoint, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setFormData(response.data);
      console.log(response.data);
    } catch (err) {
      setError("Failed to fetch user data.");
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
        <div>
          <Link to="/jobBoard" className="sidebar-link">Job Board</Link>
        </div>
        <div>
          {user?.role === "learner" && <Link to='/myTuitions' className="sidebar-link">My tuitions</Link>}
          
        </div>
      </aside>

      <main className="dashboard-main">
        <div className="stats-container">

          <div className="stat-box"> 
            <h2>0</h2> 
            <p>{user?.role === "tutor" ? "Applied Jobs" : "Applied Requests"}</p> 
          </div>
          <div className="stat-box"> 
            <h2>0</h2> 
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
