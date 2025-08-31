import { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { FaEdit } from "react-icons/fa";
import "./ProfilePage.css";

const IMMUTABLE_KEYS = [
  "id",
  "user_id",
  "email",
  "created_at",
  "updated_at",
  "email_verified_at",
];

const pickEditable = (role, data) => {
  // Adjust these to match your backend validators exactly
  const base = ["contact_number", "address", "availability"];
  const tutorOnly = [
    "full_name",
    "gender",
    "preferred_salary",
    "qualification",
    "experience",
    "currently_studying_in",
    "preferred_location",
  ];
  const learnerOnly = ["name"]; // if learners can change display name
  const allow = role === "tutor" ? base.concat(tutorOnly) : base.concat(learnerOnly);
  return allow.reduce(
    (o, k) => (k in data ? { ...o, [k]: data[k] } : o),
    {}
  );
};

const ProfilePage = () => {
  const { api, user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [profileId, setProfileId] = useState(null); // learners.id / tutors.id
  const [isEditing, setIsEditing] = useState(false);
  const [isNewProfile, setIsNewProfile] = useState(false); // create vs update
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const authHeader = {
    headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
  };

  useEffect(() => {
    if (user?.id && user?.role && token) fetchUserData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id, user?.role, token]);

  const fetchUserData = async () => {
    setError("");
    setSuccess("");
    setIsNewProfile(false);
    setIsEditing(false);
    setProfileId(null);

    try {
      // First try GET /{role}s/:id (if backend maps user_id == id)
      const direct = await api.get(
        `/api/${user.role}s/${user.id}`,
        authHeader
      );
      setFormData(direct.data || {});
      setProfileId(direct.data?.id ?? null);
    } catch (e) {
      if (e?.response?.status !== 404) {
        setError("Failed to fetch user data.");
        return;
      }

      // Fallback: GET list and find by user_id
      try {
        const list = await api.get(`/api/${user.role}s`, authHeader);
        const rec = Array.isArray(list.data)
          ? list.data.find((r) => String(r.user_id) === String(user.id))
          : null;

        if (rec) {
          setFormData(rec);
          setProfileId(rec.id);
        } else {
          // No profile yet -> prepare empty form for creation
          setFormData({ user_id: user.id });
          setIsNewProfile(true);
          setIsEditing(true);
          setError("No profile found. Please create your profile.");
        }
      } catch (err) {
        setError("Failed to fetch user data.");
      }
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    try {
      const editable = pickEditable(user.role, formData);

      if (isNewProfile) {
        // Creating new profile record; ensure user_id is included
        const payload = { ...editable, user_id: user.id };
        await api.post(`/api/${user.role}s`, payload, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        setSuccess("Profile created successfully!");
      } else {
        if (!profileId) {
          setError("Profile record not found.");
          return;
        }
        await api.put(`/api/${user.role}s/${profileId}`, editable, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        setSuccess("Profile updated successfully!");
      }

      setTimeout(() => setIsEditing(false), 1200);
      // Refresh to pick up any server-calculated fields
      fetchUserData();
    } catch (err) {
      const msg =
        err?.response?.data?.message || err?.message || "Error saving profile.";
      setError(msg);
    }
  };

  const displayPairs = Object.keys(formData)
    .filter((k) => !IMMUTABLE_KEYS.includes(k))
    .map((key) => ({
      key,
      label: key.replace(/_/g, " ").replace(/([A-Z])/g, " $1").trim(),
      value: formData[key],
    }));

  return (
    <div className="profile-page">
      <div className="profile-section">
        <img
          src="https://via.placeholder.com/100x100.png?text=Profile+Pic"
          alt="Profile"
          className="profile-pic"
        />
        <h3>{user?.name}</h3>
        <p>{user?.email}</p>
      </div>

      {error && <p className="error-message">{error}</p>}
      {success && <p className="success-message">{success}</p>}

      {!isEditing ? (
        <div className="profile-info">
          {displayPairs.length === 0 ? (
            <p>No profile data yet.</p>
          ) : (
            displayPairs.map(({ key, label, value }) => (
              <div key={key} className="info-item">
                <strong>{label}:</strong>
                <p>{value ?? "N/A"}</p>
              </div>
            ))
          )}
          <button className="edit-btn" onClick={() => setIsEditing(true)}>
            <FaEdit style={{ marginRight: 6 }} />
            {isNewProfile ? "Create" : "Edit"}
          </button>
        </div>
      ) : (
        <div className="edit-form-container">
          <h2>{isNewProfile ? "Create Profile" : "Edit Profile"}</h2>
          <form onSubmit={handleSubmit}>
            {displayPairs.map(({ key, label }) => (
              <div key={key} className="form-group">
                <label>{label}:</label>
                <input
                  type="text"
                  name={key}
                  value={formData[key] ?? ""}
                  onChange={handleInputChange}
                />
              </div>
            ))}
            <div className="button-group">
              <button type="submit" className="save-btn">
                Save
              </button>
              <button
                type="button"
                className="cancel-btn"
                onClick={() => setIsEditing(false)}
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      )}
    </div>
  );
};

export default ProfilePage;
