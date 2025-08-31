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

/**
 * Whitelist of editable fields that match your backend validators.
 * - LearnerController expects: full_name (required on POST), guardian_full_name, contact_number,
 *   guardian_contact_number, gender, address
 * - Tutor fields kept from your prior setup
 */
const pickEditable = (role, data) => {
  const tutorOnly = [
    "full_name",
    "gender",
    "preferred_salary",
    "qualification",
    "experience",
    "currently_studying_in",
    "preferred_location",
    "contact_number",
    "address",
    "availability",
  ];

  const learnerOnly = [
    "full_name",
    "guardian_full_name",
    "contact_number",
    "guardian_contact_number",
    "gender",
    "address",
  ];

  const allow = role === "tutor" ? tutorOnly : learnerOnly;
  return allow.reduce((o, k) => (k in data ? { ...o, [k]: data[k] } : o), {});
};

// extract primary key regardless of your table's naming (used for tutors if needed)
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

const DEFAULT_LEARNER_FIELDS = {
  full_name: "",
  guardian_full_name: "",
  contact_number: "",
  guardian_contact_number: "",
  gender: "",
  address: "",
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
      // Your LearnerController expects user.id in the path:
      // GET /api/learners/{userId}
      const idForShow =
        user.role === "learner" ? user.id : user.id; // keep user.id for tutors too if your TutorController is similar
      const direct = await api.get(`/api/${user.role}s/${idForShow}`, authHeader);
      const rec = direct.data || {};
      setFormData(rec);
      setProfilePk(extractProfilePk(rec, user.role));
    } catch (e) {
      // If not found, prep a "create" form
      if (e?.response?.status === 404) {
        if (user.role === "learner") {
          // Pre-fill the required full_name from user.name so POST doesn't fail validator
          setFormData({
            user_id: user.id,
            ...DEFAULT_LEARNER_FIELDS,
            full_name: user?.name || "",
          });
        } else {
          setFormData({ user_id: user.id }); // tutor defaults if needed
        }
        setIsNewProfile(true);
        setIsEditing(true);
        setError("No profile found. Please create your profile.");
        return;
      }

      setError(
        e?.response?.data?.message || e?.message || "Failed to fetch user data."
      );
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
        // LearnerController@store expects full_name (required)
        if (user.role === "learner" && !editable.full_name) {
          setError("Full name is required.");
          return;
        }

        const payload = { ...editable, user_id: user.id };
        const res = await api.post(`/api/${user.role}s`, payload, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        setSuccess(res?.data?.message || "Profile created successfully!");
      } else {
        /**
         * IMPORTANT:
         * - For learners, your controller expects /api/learners/{userId} (users.id), not LearnerID.
         * - For tutors, keep supporting profile PK if your TutorController uses PK; otherwise user.id.
         */
        const endpointId =
          user.role === "learner"
            ? user.id
            : profilePk ?? extractProfilePk(formData, user.role) ?? user.id;

        const res = await api.put(`/api/${user.role}s/${endpointId}`, editable, {
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

  // What to show as editable/display fields
  const keysForDisplay = Object.keys(formData).filter(
    (k) => !IMMUTABLE_KEYS.includes(k)
  );

  // If we're creating a brand-new learner profile, make sure the expected fields appear
  const ensureLearnerCreateKeys =
    isNewProfile && user.role === "learner" && keysForDisplay.length === 0;

  const displayPairs = (ensureLearnerCreateKeys
    ? Object.keys(DEFAULT_LEARNER_FIELDS)
    : keysForDisplay
  ).map((key) => ({
    key,
    label: key.replace(/_/g, " ").replace(/([A-Z])/g, " $1").trim(),
    value: formData[key] ?? "",
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
