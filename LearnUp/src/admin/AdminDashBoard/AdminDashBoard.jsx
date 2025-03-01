import React from 'react'
import AdminSidebar from '../AdminSidebar/AdminSidebar'
import Footer from '../../Footer/Footer'

const AdminDashBoard = () => {
  return (
    <div className="admin-container">
      <AdminSidebar />
      <div className="admin-main-content">
        
        <h2>Welcome, Admin</h2>
        <p>Manage Tutors, Learners, and Tuition Requests</p>
      </div>
      
    </div>
  )
}

export default AdminDashBoard