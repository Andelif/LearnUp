import React, { useState, useEffect, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./SignIn.css";

const SignIn = () => {
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  });
  const [passwordVisible, setPasswordVisible] = useState(false);
  
  const { setUser, setToken } = useContext(storeContext);
  const [selectedUser, setSelectedUser] = useState("learner"); // Default to learner
  const [error, setError] = useState("");
  const navigate = useNavigate();
  
  const [apiBaseUrl, setApiBaseUrl] = useState("");
  
  useEffect(() => {
    setApiBaseUrl(import.meta.env.VITE_API_BASE_URL || "http://localhost:8000");
  }, []);

  // Handle form input change
  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSignUp = () => {
    navigate('/signup');
  }

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    try {
      // Include selected role in request
      const response = await axios.post(`${apiBaseUrl}/api/login`, {
        ...formData,
        role: selectedUser, // Send selected role
      }, {
        headers: {
          "Content-Type": "application/json",
        },
      });

      const { token, user } = response.data;
      
      if (!token) {
        setError("Authentication failed! No token received.");
        return;
      }

      // Validate user role before setting session
      if (user.role !== selectedUser) {
        setError(`You are registered as a ${user.role}. Please log in with the correct role.`);
        return;
      }

      setUser(user);
      setToken(token);

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      // Redirect user based on role
      navigate("/dashboard");
      
    } catch (err) {
      if (err.response?.data?.message) {
        setError(err.response.data.message);
      } else {
        setError("Login failed. Please try again.");
      }
    }
  };

  return (
    <div className="login-container">
      <div className="main-content">
        <div className="main-content-wrapper">
          {/* Left Side Illustration */}
          <div className="illustration">
            <img src="src/assets/illustration.png" alt="Illustration" />
          </div>

          {/* Right Side Login Form */}
          <div className="login-card">
            <h2><span>Welcome</span> Back</h2>
            <p>Sign in to continue your journey.</p>

            {/* User Selection */}
            <div className="user-selection">
              <button
                className={`user-btn ${selectedUser === "learner" ? "active" : ""}`}
                onClick={() => setSelectedUser("learner")}
              >
                Learner
              </button>
              <button
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
                type="text"
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
                <span className="password-toggle" onClick={() => setPasswordVisible(!passwordVisible)}>
                  {passwordVisible ? <FaEyeSlash /> : <FaEye />}
                </span>
              </div>

              <div className="forgot-password">Forgot Password?</div>

              {error && <p className="error">{error}</p>}

              <button className="login-button" type="submit">Sign In</button>
            </form>
            
            <br />
            <div className="or-divider"><span>Or</span></div>

            <div className="signup-option">
              Don't have an account? <a className="signup-link" onClick={handleSignUp}> Sign Up</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignIn;
