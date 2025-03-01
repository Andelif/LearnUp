import React, { useState } from "react";
import { FaEye, FaEyeSlash } from "react-icons/fa";
import { useNavigate } from "react-router-dom";
import "./SignUp.css";

const SignUp = () => {
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [rePasswordVisible, setRePasswordVisible] = useState(false);
  const [selectedRole, setSelectedRole] = useState("parent");
  const navigate=useNavigate();
  const handleSignIn = () => {
    navigate('/signIn');
}
  return (
    <div className="signup-container">

      {/* Main Content */}
      <div className="main-content-wrapper">
        {/* Illustration 
        <div className="illustration">
          <img src="src/assets/illustration.png" alt="Illustration" />
        </div>  */}

        {/* Signup Form */}
        <div className="signup-card">
          <h2>
            <span>Join</span> Us
          </h2>
          <p>Sign up to find or offer tuitions.</p>

          {/* Role Selection */}
          <div className="user-selection">
            <button
              className={`user-btn ${selectedRole === "parent" ? "active" : ""}`}
              onClick={() => setSelectedRole("parent")}
            >
              Parent or Student
            </button>
            <button
              className={`user-btn ${selectedRole === "tutor" ? "active" : ""}`}
              onClick={() => setSelectedRole("tutor")}
            >
              Tutor
            </button>
          </div>

          {/* Signup Form Fields */}
          <div className="form-fields">
            <label className="input-label">Name</label>
            <input type="text" className="input-field" placeholder="Enter your name" />

            <label className="input-label">Phone</label>
            <input type="text" className="input-field" placeholder="Enter your phone number" />

            <label className="input-label">Email</label>
            <input type="email" className="input-field" placeholder="Enter your email" />

            <label className="input-label">Gender</label>
            <select className="input-field">
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>

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

            <label className="input-label">Re-enter Password</label>
            <div className="input-group">
              <input
                type={rePasswordVisible ? "text" : "password"}
                className="input-field"
                placeholder="Confirm your password"
              />
              <span
                className="password-toggle"
                onClick={() => setRePasswordVisible(!rePasswordVisible)}
              >
                {rePasswordVisible ? <FaEyeSlash /> : <FaEye />}
              </span>
            </div>

            <button className="signup-button">Sign Up</button>

            <p className="login-option">
              Already have an account? <a href="#" onClick={handleSignIn}>Sign in</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignUp;
