import React from "react";
import AdminSidebar from "./AdminSidebar/AdminSidebar";
import { Outlet } from "react-router-dom";

const AdminLayout = () => {
  return (
    <div style={{ display: "flex", height: "100vh" }}>
      <AdminSidebar />
      <div style={{ flexGrow: 1, padding: "20px", marginLeft: "220px" }}>
        <Outlet /> {/* This renders the current admin page */}
      </div>
    </div>
  );
};

export default AdminLayout;
