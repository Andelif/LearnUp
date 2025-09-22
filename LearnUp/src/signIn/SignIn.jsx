import React, { useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import "./SignIn.css";

const SignIn = () => {
  const [formData, setFormData] = useState({ email: "", password: "" });
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [selectedUser, setSelectedUser] = useState(""); // if empty, treat as "admin"
  const [error, setError] = useState("");

  const navigate = useNavigate();

  // 👇 pull the shared axios instance + setters from context
  const { api, setUser, setToken } = useContext(storeContext);

  const handleChange = (e) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSignUp = () => navigate("/signup");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    // If no selection, assume admin
    const role = selectedUser || "admin";

    try {
      const { data } = await api.post("/api/login", {
        ...formData,
        role, // harmless extra field; your backend ignores it except for admin shortcut
      });

      const { token, user, redirect } = data || {};

      if (!token) {
        setError("Authentication failed! No token received.");
        return;
      }

      // Optional role sanity check
      if (user?.role && user.role !== role) {
        setError(`You are registered as a ${user.role}. Please log in with the correct role.`);
        return;
      }

      setUser(user);
      setToken(token);
      localStorage.setItem("user", JSON.stringify(user));

      navigate(redirect || "/");
    } catch (err) {
      if (err.response?.data?.message) setError(err.response.data.message);
      else setError("Login failed. Please try again.");
    }
  };

  return (
    <div className="login-container">
      <div className="main-content">
        <div className="main-content-wrapper">
          {/* Left Side Illustration */}
          <div className="illustration">
            <img src="/assets/illustration.png" alt="Illustration" />
          </div>

          {/* Right Side Login Form */}
          <div className="login-card">
            <h2><span>Welcome</span> Back</h2>
            <p>Sign in to continue your journey.</p>

            {/* User Selection */}
            <div className="user-selection">
              <button
                type="button"
                className={`user-btn ${selectedUser === "learner" ? "active" : ""}`}
                onClick={() => setSelectedUser("learner")}
              >
                Learner
              </button>
              <button
                type="button"
                className={`user-btn ${selectedUser === "tutor" ? "active" : ""}`}
                onClick={() => setSelectedUser("tutor")}
              >
                Tutor
              </button>
            </div>

            {/* Login Form */}
            <form onSubmit={handleSubmit}>
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
                <span
                  className="password-toggle"
                  onClick={() => setPasswordVisible((v) => !v)}
                  role="button"
                  tabIndex={0}
                >
                  {passwordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>

              {/* <div
                className="forgot-password"
                role="button"
                tabIndex={0}
                onClick={() => navigate("/forgot-password")}
              >
                Forgot Password?
              </div> */}
              

              {error && <p className="error">{error}</p>}

              <button className="login-button" type="submit">Sign In</button>
            </form>

            <br />
            <div className="or-divider"><span>Or</span></div>

            <div className="signup-option">
              Don't have an account?{" "}
              <a className="signup-link" onClick={handleSignUp}>Sign Up</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignIn;
