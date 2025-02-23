import React from 'react'
import AdminSidebar from '../AdminSidebar/AdminSidebar'
import AdminNavbar from '../AdminNavbar/AdminNavbar'

const AdminDashBoard = () => {
  return (
    <div className="admin-container">
      <AdminSidebar />
      <div className="admin-main-content">
        <AdminNavbar />
        <h2>Welcome, Admin</h2>
        <p>Manage Tutors, Learners, and Tuition Requests</p>
      </div>
    </div>
  )
}

export default AdminDashBoard