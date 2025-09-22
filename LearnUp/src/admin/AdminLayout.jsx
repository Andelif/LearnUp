import React from "react";
import AdminSidebar from "./AdminSidebar/AdminSidebar";
import { Outlet } from "react-router-dom";
import "./AdminLayout.css";

const AdminLayout = () => {
  return (
    <div className="admin-layout">
      <AdminSidebar />
      <main className="admin-main-content">
        <Outlet /> 
      </main>
    </div>
  );
};

export default AdminLayout;
