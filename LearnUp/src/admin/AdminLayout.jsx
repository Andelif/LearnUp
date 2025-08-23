import React from "react";
import AdminSidebar from "./AdminSidebar/AdminSidebar";
import { Outlet } from "react-router-dom";

const AdminLayout = () => {
  return (
    <div >
      <AdminSidebar />
      <div style={{ flexGrow: 1, padding: "20px", marginLeft: "220px" }}>
        <Outlet /> 
      </div>
    </div>
  );
};

export default AdminLayout;
