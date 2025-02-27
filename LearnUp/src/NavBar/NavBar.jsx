import React, { useContext, useState } from "react";
import { Link } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import { useNavigate } from "react-router-dom";
import { FaBell } from "react-icons/fa";
import "./NavBar.css";

const NavBar = () => {
  const { user, setUser, setToken } = useContext(storeContext);
  const [showDropdown, setShowDropdown] = useState(false);
  const navigate = useNavigate();

  const handleClick=()=>{
    navigate("/");
  }

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
      <h1 className="logo" onClick={handleClick}>LearnUp</h1>
      <nav className="nav-links">
        <Link to="/">Home</Link>
        {user?.role === "learner" && <Link to="/find-tutors">Find Tutors</Link>}
        {user?.role !='admin' && <Link to="/jobBoard">JobBoard</Link>}
        
        {(user?.role === "learner" || user?.role==="tutor") && <Link to="/dashboard">Dashboard</Link>}
        {user?.role==='admin' && <Link to='/admin/dashboard'>DashBoard</Link>}
      </nav>

      

      {user ? (
        
        <div className="profile-container">
          <div className="notification-container">
            {user && (
              <Link to="/notification-center" className="notification-bell-icon">
                <FaBell  />
                {/* Optionally add a badge for unread notifications */}
                {/* <span className="notification-badge">3</span> */}
              </Link>
            )}
          </div>
          <div className="profile-section" onClick={() => setShowDropdown(!showDropdown)}>
            
              <span className="profile-icon">👤</span>
              <br />
              <span className="user-name">{user.name}</span> {/* ✅ Show username */}
            
          </div>

          {showDropdown && (
            <div className="dropdown-menu">
            <Link to="/ProfilePage" className="dropdown-item">Profile</Link> {/* Link to profile page */}
            <button className="dropdown-item" onClick={handleLogout}>Logout</button> {/* Logout button */}
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
