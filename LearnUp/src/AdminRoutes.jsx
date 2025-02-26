import React, { useContext } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import AdminDashBoard from "./admin/AdminDashBoard/AdminDashBoard";
import ManageTutors from "./admin/ManageTutors/ManageTutors";
import { storeContext } from "./context/contextProvider";
import AdminLayout from "./admin/AdminLayout";
import ManageLearners from "./admin/ManageLearners/ManageLearners";

const AdminRoutes = () => {
  const { user } = useContext(storeContext);
  const isAdmin = user?.role === "admin"; // Ensure this is correctly set

  if (!isAdmin) {
    return <Navigate to="/" replace />; // Redirect to home if not admin
  }

  return (
    <Routes>
      <Route element={<AdminLayout/>}>
        <Route path="dashboard" element={<AdminDashBoard />} />
        <Route path="manage-tutors" element={<ManageTutors />} />
        <Route path="manage-learners" element={<ManageLearners />} />
     </Route>
    </Routes>
  );
};

export default AdminRoutes;
