import React from "react";
import { Link, useLocation } from "react-router-dom";
import { FaChalkboardTeacher, FaUsers, FaUserTie, FaChartBar } from "react-icons/fa";
import "./AdminSidebar.css";

const AdminSidebar = () => {
  const location = useLocation();

  const isActive = (path) => {
    return location.pathname === path;
  };

  return (
    <aside className="admin-sidebar">
      <h2>ADMIN PANEL</h2>
      <nav>
        <ul>
          <li>
            <Link 
              to="/admin/manage-tutors" 
              className={isActive('/admin/manage-tutors') ? 'active' : ''}
            >
              <FaChalkboardTeacher className="sidebar-icon" /> Manage Tutors
            </Link>
          </li>
          <li>
            <Link 
              to="/admin/manage-learners" 
              className={isActive('/admin/manage-learners') ? 'active' : ''}
            >
              <FaUsers className="sidebar-icon" /> Manage Learners
            </Link>
          </li>
          <li>
            <Link 
              to="/admin/match-learner-tutor" 
              className={isActive('/admin/match-learner-tutor') ? 'active' : ''}
            >
              <FaUserTie className="sidebar-icon" /> Match Learner to Tutors
            </Link>
          </li>
          <li>
            <Link 
              to="/admin/dashboard" 
              className={isActive('/admin/dashboard') ? 'active' : ''}
            >
              <FaChartBar className="sidebar-icon" /> Dashboard
            </Link>
          </li>
        </ul>
      </nav>
    </aside>
  );
};

export default AdminSidebar;
