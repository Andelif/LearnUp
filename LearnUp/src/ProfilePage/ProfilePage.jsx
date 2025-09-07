import { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import axios from "axios";
import './ProfilePage.css';

const ProfilePage = () => {
  const { user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [isEditing, setIsEditing] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

  const fieldNames = {
    id: "User ID",
    tutor_id: "Tutor ID",
    userId: "User ID",
    name: "Full Name",
    email: "Email Address",
    contact_number: "Contact Number",
    address: "Address",
    qualification: "Qualification",
    experience: "Experience",
    salary: "Preferred Salary",
    availability: "Availability",
  };

  const nonEditableFields = ["id", "tutor_id", "userId"]; // Fields that cannot be edited

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
    if (!nonEditableFields.includes(name)) {
      setFormData((prevData) => ({ ...prevData, [name]: value }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    // Ensure IDs are not sent/modified
    const updateData = { ...formData };
    nonEditableFields.forEach((field) => delete updateData[field]);

    try {
      await axios.put(`${apiBaseUrl}/api/${user.role}s/${user.id}`, updateData, {
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
        <img src="src/assets/images.jpg" alt="Profile" className="profile-pic" />
        <h3>{user?.name}</h3>
        <p>{user?.email}</p>
      </div>

      {error && <p className="error-message">{error}</p>}
      {success && <p className="success-message">{success}</p>}

      {!isEditing ? (
        <div className="profile-info">
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
                  readOnly={nonEditableFields.includes(key)}
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
