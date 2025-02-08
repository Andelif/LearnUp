import React, { useState, useContext, useEffect } from "react";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import { storeContext } from "../context/contextProvider";
import "./SignUp.css";

const SignUp = () => {
  const [formData, setFormData] = useState({
    name: "",
    phone: "",
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
  const url = useContext(storeContext);

  // Ensure base URL is correct
  const [apiBaseUrl, setApiBaseUrl] = useState("");

  useEffect(() => {
    setApiBaseUrl(import.meta.env.VITE_API_BASE_URL || "http://localhost:8000");
  }, []);

  const handleSignIn = () => {
    navigate("/signIn");
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");
    console.log("Submitting form data:", formData);

    try {
      const response = await axios.post(`${apiBaseUrl}/api/register`, formData, {
        headers: {
          "Content-Type": "application/json",
        },
        withCredentials: true,
      });

      setSuccess("Registration successful! Redirecting...");
      setTimeout(() => navigate("/signIn"), 2000);
    } catch (err) {
      if (err.response?.data?.errors) {
        const errorMessages = Object.values(err.response.data.errors).flat().join(" ");
        setError(errorMessages);
      } else {
        setError("Registration failed. Please try again.");
      }
    }
  };

  return (
    <div className="signup-container">
      <div className="main-content-wrapper">
        <div className="signup-card">
          <h2>
            <span>Join</span> Us
          </h2>
          <p>Sign up to find or offer tuitions.</p>

          <div className="user-selection">
            <button
              className={`user-btn ${formData.role === "learner" ? "active" : ""}`}
              onClick={() => setFormData({ ...formData, role: "learner" })}
            >
              Parent or Student
            </button>
            <button
              className={`user-btn ${formData.role === "tutor" ? "active" : ""}`}
              onClick={() => setFormData({ ...formData, role: "tutor" })}
            >
              Tutor
            </button>
          </div>

          <div className="form-fields">
            <form onSubmit={handleSubmit}>
              <label className="input-label">Name</label>
              <input
                type="text"
                name="name"
                className="input-field"
                placeholder="Enter your name"
                value={formData.name}
                onChange={handleChange}
                required
              />

              <label className="input-label">Phone</label>
              <input
                type="text"
                name="phone"
                className="input-field"
                placeholder="Enter your phone number"
                value={formData.phone}
                onChange={handleChange}
                required
              />

              <label className="input-label">Email</label>
              <input
                type="email"
                name="email"
                className="input-field"
                placeholder="Enter your email"
                value={formData.email}
                onChange={handleChange}
                required
              />

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

              <label className="input-label">Password</label>
              <div className="input-group">
                <input
                  type={passwordVisible ? "text" : "password"}
                  name="password"
                  className="input-field"
                  placeholder="Enter your password"
                  value={formData.password}
                  onChange={handleChange}
                  required
                />
                <span className="password-toggle" onClick={() => setPasswordVisible(!passwordVisible)}>
                  {passwordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>

              <label className="input-label">Re-enter Password</label>
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
                <span className="password-toggle" onClick={() => setRePasswordVisible(!rePasswordVisible)}>
                  {rePasswordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>

              {error && <p className="error">{error}</p>}
              {success && <p className="success">{success}</p>}

              <button className="signup-button" type="submit">Sign Up</button>

              <p className="login-option">
                Already have an account? <a href="#" onClick={handleSignIn}>Sign in</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignUp;
