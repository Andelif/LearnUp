import React, { useState, useContext } from "react";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import "./SignUp.css";

const SignUp = () => {
  const [formData, setFormData] = useState({
    name: "",
    contact_number: "",
    email: "",
    gender: "male",
    password: "",
    password_confirmation: "",
    role: "learner",
  });
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [rePasswordVisible, setRePasswordVisible] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const navigate = useNavigate();
  const { api } = useContext(storeContext);

  const handleSignIn = () => navigate("/signIn");

  const handleChange = (e) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (formData.password !== formData.password_confirmation) {
      setError("Passwords do not match.");
      return;
    }

    try {
      const res = await api.post("/api/register", formData, {
        headers: { "Content-Type": "application/json" },
      });

      const { token, user } = res.data;
      localStorage.setItem("token", token);
      
      setSuccess("Registration successful! Redirecting...");
      setTimeout(() => navigate("/signIn"), 2000);
    } catch (err) {
      const data = err.response?.data;
      const msg =
        (data?.errors && Object.values(data.errors).flat().join(" ")) ||
        data?.error ||
        data?.details ||
        data?.message ||
        "Registration failed. Please try again.";
      setError(msg);
    }
  };

  return (
    <div className="signup-container">
      <div className="signup-card">
        <div className="signup-header">
          <h2>Create Your Account</h2>
          <p>Join LearnUp to find or offer tuitions</p>
        </div>

        <div className="user-selection">
          <button
            type="button"
            className={`user-btn ${formData.role === "learner" ? "active" : ""}`}
            onClick={() => setFormData((p) => ({ ...p, role: "learner" }))}
          >
            <span className="btn-icon">🎓</span>
            Learner
          </button>
          <button
            type="button"
            className={`user-btn ${formData.role === "tutor" ? "active" : ""}`}
            onClick={() => setFormData((p) => ({ ...p, role: "tutor" }))}
          >
            <span className="btn-icon">📚</span>
            Tutor
          </button>
        </div>

        <form onSubmit={handleSubmit} className="signup-form">
          <div className="form-row">
            <div className="form-group">
              <label className="input-label">Full Name</label>
              <input
                type="text"
                name="name"
                className="input-field"
                placeholder="Enter your full name"
                value={formData.name}
                onChange={handleChange}
                required
              />
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label className="input-label">Phone Number</label>
              <input
                type="text"
                name="contact_number"
                className="input-field"
                placeholder="Enter your phone number"
                value={formData.contact_number}
                onChange={handleChange}
                required
              />
            </div>
            
            <div className="form-group">
              <label className="input-label">Email Address</label>
              <input
                type="email"
                name="email"
                className="input-field"
                placeholder="Enter your email"
                value={formData.email}
                onChange={handleChange}
                required
              />
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label className="input-label">Gender</label>
              <select
                name="gender"
                className="input-field"
                value={formData.gender}
                onChange={handleChange}
              >
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label className="input-label">Password</label>
              <div className="input-group">
                <input
                  type={passwordVisible ? "text" : "password"}
                  name="password"
                  className="input-field"
                  placeholder="Create a password"
                  value={formData.password}
                  onChange={handleChange}
                  required
                />
                <span
                  className="password-toggle"
                  onClick={() => setPasswordVisible((v) => !v)}
                  role="button"
                  tabIndex={0}
                >
                  {passwordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>
            </div>
            
            <div className="form-group">
              <label className="input-label">Confirm Password</label>
              <div className="input-group">
                <input
                  type={rePasswordVisible ? "text" : "password"}
                  name="password_confirmation"
                  className="input-field"
                  placeholder="Confirm your password"
                  value={formData.password_confirmation}
                  onChange={handleChange}
                  required
                />
                <span
                  className="password-toggle"
                  onClick={() => setRePasswordVisible((v) => !v)}
                  role="button"
                  tabIndex={0}
                >
                  {rePasswordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>
            </div>
          </div>

          {error && <div className="error-message">{error}</div>}
          {success && <div className="success-message">{success}</div>}

          <button className="signup-button" type="submit">
            Create Account
          </button>

          <div className="login-redirect">
            <p>Already have an account? <span onClick={handleSignIn}>Sign in</span></p>
          </div>
        </form>
      </div>
    </div>
  );
};

export default SignUp;