import React from "react";
import { Link } from "react-router-dom";
import { FaChalkboardTeacher, FaUsers } from "react-icons/fa";
import "./AdminSidebar.css";

const AdminSidebar = () => {
  return (
    <aside className="admin-sidebar">
      <h2>ADMIN PANEL</h2>
      <nav>
        <ul>
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
