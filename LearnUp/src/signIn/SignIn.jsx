import React, { useState } from "react";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { AiOutlineMail, AiOutlineLock } from "react-icons/ai";
import { useNavigate } from "react-router-dom";
import "./SignIn.css"; // Import the CSS file

const SignIn = () => {
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [selectedUser, setSelectedUser] = useState("parent");
  const navigate=useNavigate();
  const handleSignUp = () => {
    navigate('/signup');
}

  return (
    <div className="login-container">
        
      
      {/* Main Content */}
      <div className="main-content">
        <div className="main-content-wrapper">
          {/* Left Side Illustration Placeholder */}
          <div className="illustration">
            <img src="src/assets/illustration.png" alt="Illustration" />
          </div>

          {/* Right Side Login Form */}
          <div className="login-card">
            <h2>
              <span>Welcome</span> Back
            </h2>
            <p>Sign in to Continue your Journey.</p>

            {/* User Selection */}
            <div className="user-selection">
              <button
                className={`user-btn ${selectedUser === "parent" ? "active" : ""}`}
                onClick={() => setSelectedUser("parent")}
              >
                Parents or Student
              </button>
              <button
                className={`user-btn ${selectedUser === "tutor" ? "active" : ""}`}
                onClick={() => setSelectedUser("tutor")}
              >
                Tutor
              </button>
            </div>

            {/* Login Form */}
            <label className="input-label">Phone/Email</label>
            <div className="input-group">
              
              <input type="text" className="input-field" placeholder="Enter your email" />
            </div>

            <label className="input-label">Password</label>
            <div className="input-group">
              
              <input
                type={passwordVisible ? "text" : "password"}
                className="input-field"
                placeholder="Enter your password"
              />
              <span
                className="password-toggle"
                onClick={() => setPasswordVisible(!passwordVisible)}
              >
                {passwordVisible ? <FaEyeSlash /> : <FaEye />}
              </span>
            </div>

            <div className="forgot-password">Forgot Password?</div>

            <button className="login-button">Sign In</button>
          </div>
        </div>
        <div className="or-divider">
    <span>Or</span>
  </div>

  {/* Sign Up Link */}
  <div className="signup-option">
    Don't have an account? <a href="/signup" className="signup-link" onClick={handleSignUp}>Sign Up</a>
  </div>
      </div>
    </div>
  );
};

export default SignIn;
