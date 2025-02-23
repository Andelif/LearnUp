import "./Dashboard.css";
import { useContext, useState, useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice, FaEdit } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";
import { Link } from "react-router-dom";
import axios from "axios";

const Dashboard = () => {
  const [userType, setUserType] = useState("tutor");
  const { user, token } = useContext(storeContext);
  const [isEditing, setIsEditing] = useState(false);
  const [formData, setFormData] = useState({});
  const [apiBaseUrl, setApiBaseUrl] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

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
    } catch (err) {
      setError("Failed to fetch user data.");
    }
  };

  const handleEditClick = () => {
    setIsEditing(!isEditing);
    fetchUserData();
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({ ...prevData, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");
    const updatedFields = Object.fromEntries(
      Object.entries(formData).filter(([_, value]) => value !== "")
    );
    const endpoint = user.role === "tutor"
      ? `${apiBaseUrl}/api/tutors/${user.id}`
      : `${apiBaseUrl}/api/learners/${user.id}`;

    try {
      await axios.put(endpoint, updatedFields, {
        headers: { "Content-Type": "application/json", Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setSuccess("Profile updated successfully!");
      setTimeout(() => setIsEditing(false), 2000);
    } catch (err) {
      setError("Error updating profile. Please try again.");
    }
  };

  return (
    <div className="dashboard-container">
      <aside className="sidebar">
        <div className="profile-section">
          <img src="https://via.placeholder.com/80" alt="Profile" className="profile-pic" />
          <FaEdit className="edit-icon" onClick={handleEditClick} />
          <h3>{user?.name}</h3>
          <p>{user?.email}</p>
        </div>
        <div>
          <Link to="/jobBoard" className="sidebar-link">Job Board</Link>
        </div>
      </aside>

      {isEditing && (
        <div className="edit-form-modal">
          <div className="edit-form-container">
            <h2>Edit Profile</h2>
            <form onSubmit={handleSubmit}>
              {Object.keys(formData).map((key) => (
                <div key={key} className="form-group">
                  <label>{fieldNames[key] || key.replace(/([A-Z])/g, " $1").trim()}:</label>
                  <input
                    type="text"
                    name={key}
                    value={formData[key]}
                    onChange={handleInputChange}
                  />
                </div>
              ))}
              <button type="submit">Save</button>
              <button type="button" onClick={() => setIsEditing(false)}>Cancel</button>
            </form>
          </div>
        </div>
      )}

      <main className="dashboard-main">
        <div className="stats-container">
          <div className="stat-box"> <h2>25</h2> <p>{userType === "tutor" ? "Applied Jobs" : "Applied Requests"}</p> </div>
          <div className="stat-box"> <h2>10</h2> <p>{userType === "tutor" ? "Shortlisted Jobs" : "Shortlisted Tutors"}</p> </div>
          <div className="stat-box"> <h2>2</h2> <p>{userType === "tutor" ? "Appointed Jobs" : "Appointed Tutors"}</p> </div>
          <div className="stat-box"> <h2>3</h2> <p>{userType === "tutor" ? "Confirmed Jobs" : "Confirmed Tutors"}</p> </div>
          <div className="stat-box"> <h2>9</h2> <p>{userType === "tutor" ? "Cancelled Jobs" : "Cancelled Tutors"}</p> </div>
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
