import { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { FaEdit } from "react-icons/fa";
import "./ProfilePage.css";

const IMMUTABLE_KEYS = [
  "id",
  "TutorID",
  "LearnerID",
  "tutor_id",
  "learner_id",
  "user_id",
  "email",
  "created_at",
  "updated_at",
  "email_verified_at",
];

const pickEditable = (role, data) => {
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
  const learnerOnly = ["name"]; // adjust if learners can edit more
  const allow = role === "tutor" ? base.concat(tutorOnly) : base.concat(learnerOnly);
  return allow.reduce((o, k) => (k in data ? { ...o, [k]: data[k] } : o), {});
};

// extract primary key regardless of your table's naming
const extractProfilePk = (rec, role) => {
  if (!rec || typeof rec !== "object") return null;
  return (
    rec.id ??
    rec.TutorID ??
    rec.LearnerID ??
    rec.tutor_id ??
    rec.learner_id ??
    null
  );
};

const ProfilePage = () => {
  const { api, user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [profilePk, setProfilePk] = useState(null);
  const [isEditing, setIsEditing] = useState(false);
  const [isNewProfile, setIsNewProfile] = useState(false);
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
    setProfilePk(null);

    try {
      // Try: /{role}s/:id (some backends map this to user id)
      const direct = await api.get(`/api/${user.role}s/${user.id}`, authHeader);
      const rec = direct.data || {};
      setFormData(rec);
      setProfilePk(extractProfilePk(rec, user.role));
    } catch (e) {
      if (e?.response?.status !== 404) {
        setError(
          e?.response?.data?.message || e?.message || "Failed to fetch user data."
        );
        return;
      }

      // Fallback: list all and find by user_id
      try {
        const list = await api.get(`/api/${user.role}s`, authHeader);
        const rec = Array.isArray(list.data)
          ? list.data.find((r) => String(r.user_id) === String(user.id))
          : null;

        if (rec) {
          setFormData(rec);
          setProfilePk(extractProfilePk(rec, user.role));
        } else {
          setFormData({ user_id: user.id });
          setIsNewProfile(true);
          setIsEditing(true);
          setError("No profile found. Please create your profile.");
        }
      } catch (err) {
        setError(
          err?.response?.data?.message || err?.message || "Failed to fetch user data."
        );
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
        const payload = { ...editable, user_id: user.id };
        const res = await api.post(`/api/${user.role}s`, payload, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        setSuccess(res?.data?.message || "Profile created successfully!");
      } else {
        const pk = profilePk ?? extractProfilePk(formData, user.role);
        if (!pk) {
          setError("Profile record not found.");
          return;
        }
        const res = await api.put(`/api/${user.role}s/${user.id}`, editable, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        setSuccess(res?.data?.message || "Profile updated successfully!");
      }

      setTimeout(() => setIsEditing(false), 1000);
      fetchUserData();
    } catch (err) {
      const msg =
        err?.response?.data?.message ||
        err?.response?.data?.error ||
        err?.message ||
        "Error saving profile.";
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
        <h2 style={{ textAlign: "center" }}>{user?.name}</h2>
        <p style={{ textAlign: "center" }}>{user?.email}</p>
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
