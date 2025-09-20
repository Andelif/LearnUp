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

/** Whitelist fields */
const pickEditable = (role, data) => {
  const tutorOnly = [
    "full_name",
    "gender",
    "preferred_salary",
    "qualification",
    "experience",
    "currently_studying_in",
    "preferred_location",
    "preferred_time",
    "contact_number",
    "address",
    "availability",
    "profile_picture",
  ];

  const learnerOnly = [
    "full_name",
    "guardian_full_name",
    "contact_number",
    "guardian_contact_number",
    "gender",
    "address",
    "profile_picture",
  ];

  const allow = role === "tutor" ? tutorOnly : learnerOnly;
  return allow.reduce((o, k) => (k in data ? { ...o, [k]: data[k] } : o), {});
};

const DEFAULT_LEARNER_FIELDS = {
  full_name: "",
  guardian_full_name: "",
  contact_number: "",
  guardian_contact_number: "",
  gender: "",
  address: "",
  profile_picture: null,
};

const DEFAULT_TUTOR_FIELDS = {
  full_name: "",
  address: "",
  contact_number: "",
  gender: "",
  preferred_salary: "",
  qualification: "",
  experience: "",
  currently_studying_in: "",
  preferred_location: "",
  preferred_time: "",
  availability: true,
  profile_picture: null,
};

const ProfilePage = () => {
  const { api, user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [isEditing, setIsEditing] = useState(false);
  const [isNewProfile, setIsNewProfile] = useState(false);
  const [error, setError] = useState("");
  const [validationErrors, setValidationErrors] = useState([]);
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
    setValidationErrors([]);
    setSuccess("");
    setIsNewProfile(false);
    setIsEditing(false);

    try {
      const { data } = await api.get(
        `/api/${user.role}s/${user.id}`,
        authHeader
      );
      setFormData(data || {});
    } catch (e) {
      if (e?.response?.status === 404) {
        const base =
          user.role === "tutor"
            ? { ...DEFAULT_TUTOR_FIELDS }
            : { ...DEFAULT_LEARNER_FIELDS, full_name: user?.name || "" };

        setFormData({ user_id: user.id, ...base });
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
    const { name, value, type, checked, files } = e.target;
    if (type === "file") {
      setFormData((prev) => ({
        ...prev,
        [name]: files[0],
      }));
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: type === "checkbox" ? checked : value,
      }));
    }
  };

  const normalizeForSubmit = (role, editable) => {
    if (role !== "tutor") return editable;
    const out = { ...editable };
    if (out.preferred_salary !== undefined && out.preferred_salary !== "") {
      const n = Number(out.preferred_salary);
      if (!Number.isNaN(n)) out.preferred_salary = n;
    }
    if (out.availability !== undefined) {
      if (typeof out.availability === "string") {
        const s = out.availability.toLowerCase().trim();
        if (["true", "1", "yes"].includes(s)) out.availability = true;
        else if (["false", "0", "no"].includes(s)) out.availability = false;
      }
    }
    return out;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setValidationErrors([]);
    setSuccess("");

    try {
      let editable = pickEditable(user.role, formData);
      editable = normalizeForSubmit(user.role, editable);

      let payload;
      let headers;

      if (editable.profile_picture instanceof File) {
        payload = new FormData();
        Object.keys(editable).forEach((k) => {
          if (editable[k] !== null && editable[k] !== undefined) {
            payload.append(k, editable[k]);
          }
        });
        headers = {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
          "Content-Type": "multipart/form-data",
        };
      } else {
        payload = editable;
        headers = {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        };
      }

      let res;
      if (isNewProfile) {
        res = await api.post(`/api/${user.role}s`, payload, { headers });
      } else {
        res = await api.put(`/api/${user.role}s/${user.id}`, payload, {
          headers,
        });
      }

      setSuccess(res?.data?.message || "Profile saved!");
      setTimeout(() => setIsEditing(false), 1000);
      fetchUserData();
    } catch (err) {
      const res = err?.response?.data;
      if (res?.errors) {
        // Laravel validation errors
        const errorsList = [];
        Object.keys(res.errors).forEach((field) => {
          res.errors[field].forEach((msg) => {
            errorsList.push(`${field}: ${msg}`);
          });
        });
        setValidationErrors(errorsList);
      }
      setError(
        res?.message ||
          res?.error ||
          err?.message ||
          "Error saving profile."
      );
    }
  };

  const keysForDisplay = Object.keys(formData).filter(
    (k) => !IMMUTABLE_KEYS.includes(k)
  );

  const ensureDefaults =
    isNewProfile && keysForDisplay.length === 0
      ? user.role === "tutor"
        ? Object.keys(DEFAULT_TUTOR_FIELDS)
        : Object.keys(DEFAULT_LEARNER_FIELDS)
      : keysForDisplay;

  const displayPairs = ensureDefaults.map((key) => ({
    key,
    label: key.replace(/_/g, " ").replace(/([A-Z])/g, " $1").trim(),
    value:
      formData[key] ??
      (user.role === "tutor"
        ? DEFAULT_TUTOR_FIELDS[key]
        : DEFAULT_LEARNER_FIELDS[key]),
  }));

  return (
    <div className="profile-page">
      <div className="profile-section">
        <img
          src={
            formData.profile_picture
              ? formData.profile_picture instanceof File
                ? URL.createObjectURL(formData.profile_picture)
                : formData.profile_picture
              : "/assets/profile.jpg"
          }
          alt="Profile"
          className="profile-pic"
        />
        <h2 style={{ textAlign: "center" }}>{user?.name}</h2>
        <p style={{ textAlign: "center" }}>{user?.email}</p>
      </div>

      {/* Error + Success messages */}
      {error && <p className="error-message">{error}</p>}
      {validationErrors.length > 0 && (
        <ul className="error-list">
          {validationErrors.map((err, idx) => (
            <li key={idx}>{err}</li>
          ))}
        </ul>
      )}
      {success && <p className="success-message">{success}</p>}

      {!isEditing ? (
        <div className="profile-info">
          {displayPairs.length === 0 ? (
            <p>No profile data yet.</p>
          ) : (
            displayPairs.map(({ key, label, value }) => (
              <div key={key} className="info-item">
                <strong>{label}:</strong>
                <p>{String(value ?? "N/A")}</p>
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
            {displayPairs.map(({ key, label }) => {
              if (user.role === "tutor" && key === "availability") {
                return (
                  <div key={key} className="form-group">
                    <label>{label}:</label>
                    <input
                      type="checkbox"
                      name={key}
                      checked={!!formData[key]}
                      onChange={handleInputChange}
                    />
                  </div>
                );
              }
              if (key === "profile_picture") {
                return (
                  <div key={key} className="form-group">
                    <label>{label}:</label>
                    <input
                      type="file"
                      name={key}
                      accept="image/*"
                      onChange={handleInputChange}
                    />
                  </div>
                );
              }
              return (
                <div key={key} className="form-group">
                  <label>{label}:</label>
                  <input
                    type="text"
                    name={key}
                    value={formData[key] ?? ""}
                    onChange={handleInputChange}
                  />
                </div>
              );
            })}
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
