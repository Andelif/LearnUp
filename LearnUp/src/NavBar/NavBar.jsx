import React, { useContext, useState } from "react";
import { Link } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import { useNavigate } from "react-router-dom";
import "./NavBar.css";

const NavBar = () => {
  const { user, setUser, setToken } = useContext(storeContext);
  const [showDropdown, setShowDropdown] = useState(false);
  const navigate = useNavigate();

  const handleLogout = () => {
    setUser(null);
    setToken(null);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setShowDropdown(false);
    navigate("/");
  };

  return (
    <header className="header">
      <h1 className="logo">LearnUp</h1>
      <nav className="nav-links">
        <Link to="/">Home</Link>
        {user?.role === "learner" && <Link to="/find-tutors">Find Tutors</Link>}
        <Link>About Us</Link>
        <Link>Contact</Link>
        <Link to="/dashboard">Dashboard</Link>
      </nav>

      {user ? (
        <div className="profile-container">
          <div className="profile-section" onClick={() => setShowDropdown(!showDropdown)}>
            <span className="profile-icon">👤</span>
            <span className="user-name">{user.name}</span> {/* ✅ Show username */}
          </div>

          {showDropdown && (
            <div className="dropdown-menu">
              <button className="dropdown-item" onClick={handleLogout}>
                Logout
              </button>
            </div>
          )}
        </div>
      ) : (
        <Link to="/signIn">
          <button className="sign-in-btn">Sign In</button>
        </Link>
      )}
    </header>
  );
};

export default NavBar;
