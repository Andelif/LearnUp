import { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import axios from "axios";
import { FaEdit } from "react-icons/fa";
import './ProfilePage.css';

const ProfilePage = () => {
  const { user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [isEditing, setIsEditing] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

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
    if (user) {
      fetchUserData();
    }
  }, [user]);

  const fetchUserData = async () => {
    try {
      const endpoint = `${apiBaseUrl}/api/${user.role}s/${user.id}`;
      const response = await axios.get(endpoint, {
        headers: { Authorization: `Bearer ${token}` },
        withCredentials: true,
      });
      setFormData(response.data);
    } catch (err) {
      setError("Failed to fetch user data.");
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({ ...prevData, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");
    
    try {
      await axios.put(`${apiBaseUrl}/api/${user.role}s/${user.id}`, formData, {
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
    <div className="profile-page">
      <div className="profile-section">
        <img src="https://via.placeholder.com/100x100.png?text=Profile+Pic" alt="Profile" className="profile-pic" />
        <h3>{user?.name}</h3>
        <p>{user?.email}</p>
      </div>

      {error && <p className="error-message">{error}</p>}
      {success && <p className="success-message">{success}</p>}

      {!isEditing ? (
        <div className="profile-info">
          {/* Displaying user info */}
          {Object.keys(formData).map((key) => (
            <div key={key} className="info-item">
              <strong>{fieldNames[key] || key.replace(/([A-Z])/g, " $1").trim()}:</strong>
              <p>{formData[key] || "N/A"}</p>
            </div>
          ))}
          <button className="edit-btn" onClick={() => setIsEditing(true)}>Edit</button>
        </div>
      ) : (
        <div className="edit-form-container">
          <h2>Edit Profile</h2>
          <form onSubmit={handleSubmit}>
            {Object.keys(formData).map((key) => (
              <div key={key} className="form-group">
                <label>{fieldNames[key] || key.replace(/([A-Z])/g, " $1").trim()}:</label>
                <input
                  type="text"
                  name={key}
                  value={formData[key] || ""}
                  onChange={handleInputChange}
                />
              </div>
            ))}
            <div className="button-group">
              <button type="submit" className="save-btn">Save</button>
              <button type="button" className="cancel-btn" onClick={() => setIsEditing(false)}>Cancel</button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
};

export default ProfilePage;
