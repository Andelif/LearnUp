import React from "react";
import { Link } from "react-router-dom";
import { FaUserShield, FaChalkboardTeacher, FaUsers, FaCog } from "react-icons/fa";
import "./AdminSidebar.css";

const AdminSidebar = () => {
  return (
    <aside className="admin-sidebar">
      <div className="profile-section">
        <img src="https://via.placeholder.com/80" alt="Admin Profile" className="profile-pic" />
        <h3>Admin Panel</h3>
      </div>
      <nav>
        <ul>
          <li>
            <Link to="/admin/dashboard">
              <FaUserShield className="sidebar-icon" /> Dashboard
            </Link>
          </li>
          <li>
            <Link to="/admin/manage-tutors">
              <FaChalkboardTeacher className="sidebar-icon" /> Manage Tutors
            </Link>
          </li>
          <li>
            <Link to="/admin/manage-learners">
              <FaUsers className="sidebar-icon" /> Manage Learners
            </Link>
          </li>
          <li>
            <Link to="/admin/match-learner-tutor">
              <FaUsers className="sidebar-icon" /> Match Learner to Tutors
            </Link>
          </li>
          
        </ul>
      </nav>
    </aside>
  );
};

export default AdminSidebar;
