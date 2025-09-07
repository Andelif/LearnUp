import React, { useContext, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import { FaBell } from "react-icons/fa";
import "./NavBar.css";

const NavBar = () => {
  const { api, user, setUser, setToken } = useContext(storeContext);
  const [showDropdown, setShowDropdown] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const navigate = useNavigate();

  const handleClick = () => navigate("/");

  const handleLogout = async () => {
    try { await api.post("/api/logout"); } catch (_) {}
    setUser(null);
    setToken(null);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setShowDropdown(false);
    setMenuOpen(false);
    navigate("/");
  };

  const closeMenu = () => setMenuOpen(false);

  // Close on Esc key + lock scroll when menu is open (mobile)
  useEffect(() => {
    const onKey = (e) => { if (e.key === "Escape") { setMenuOpen(false); setShowDropdown(false); } };
    window.addEventListener("keydown", onKey);

    const isMobile = window.matchMedia("(max-width: 768px)").matches;
    if (menuOpen && isMobile) document.body.classList.add("no-scroll");
    else document.body.classList.remove("no-scroll");

    return () => {
      window.removeEventListener("keydown", onKey);
      document.body.classList.remove("no-scroll");
    };
  }, [menuOpen]);

  // If user rotates/resizes to desktop, ensure the menu closes
  useEffect(() => {
    const onResize = () => {
      if (window.innerWidth > 768) setMenuOpen(false);
    };
    window.addEventListener("resize", onResize);
    return () => window.removeEventListener("resize", onResize);
  }, []);

  return (
    <header className="header">
      <h1 className="logo" onClick={handleClick}>LearnUp</h1>

      {/* Hamburger (mobile only via CSS) */}
      <button
        className={`menu-btn ${menuOpen ? "active" : ""}`}
        aria-label="Toggle menu"
        aria-expanded={menuOpen}
        aria-controls="primary-navigation"
        onClick={() => setMenuOpen(v => !v)}
      >
        ☰
      </button>

      <nav id="primary-navigation" className={`nav-links ${menuOpen ? "open" : ""}`}>
        <Link to="/" onClick={closeMenu}>Home</Link>
        {user?.role === "learner" && (
          <Link to="/find-tutors" onClick={closeMenu}>Find Tutors</Link>
        )}
        {user?.role !== "admin" && (
          <Link to="/jobBoard" onClick={closeMenu}>JobBoard</Link>
        )}
        {(user?.role === "learner" || user?.role === "tutor") && (
          <Link to="/dashboard" onClick={closeMenu}>Dashboard</Link>
        )}
        {user?.role === "admin" && (
          <Link to="/admin/dashboard" onClick={closeMenu}>DashBoard</Link>
        )}
      </nav>

      {user ? (
        <div className="profile-container">
          <div className="notification-container">
            <Link to="/notification-center" className="notification-bell-icon" onClick={closeMenu}>
              <FaBell />
            </Link>
          </div>

          <div className="profile-sections" onClick={() => setShowDropdown(v => !v)}>
            <span className="profile-icon">👤</span>
            <br />
            <span className="user-name">{user.name}</span>
          </div>

          {showDropdown && (
            <div className="dropdown-menu">
              <Link
                to="/ProfilePage"
                className="dropdown-item"
                onClick={() => { setShowDropdown(false); closeMenu(); }}
              ><b>
                Profile</b>
              </Link>
              <button className="dropdown-item" onClick={handleLogout}>
                Logout
              </button>
            </div>
          )}
        </div>
      ) : (
        <Link to="/signIn" onClick={closeMenu}>
          <button className="sign-in-btn">Sign In</button>
        </Link>
      )}

      {/* Backdrop overlay for mobile menu / dropdown */}
      {menuOpen && (
        <div
          className="nav-overlay"
          role="presentation"
          onClick={() => { setMenuOpen(false); setShowDropdown(false); }}
        />
      )}
    </header>
  );
};

export default NavBar;
