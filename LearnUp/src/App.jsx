import './App.css';
import { BrowserRouter as Router } from "react-router-dom";
import React, { useContext } from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';
import { storeContext } from './context/contextProvider';
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import AdminSidebar from './admin/AdminSidebar/AdminSidebar';

const AppContent = () => {
  const { user } = useContext(storeContext);
  const isAdmin = user?.role === "admin";

  return (
    <Router>
      <div className="app-container">
        <NavBar />
        <div className="main-content">
          {isAdmin && <AdminSidebar />}
          <AppRoutes />
        </div>
        {!isAdmin && <Footer />}
      </div>
      <ToastContainer
        position="top-right"
        autoClose={3000}
        hideProgressBar={false}
        closeOnClick
        pauseOnHover
        draggable
        theme="colored"
      />
    </Router>
  );
};

const App = () => {
  return (
    <ContextProvider>
      <AppContent />
    </ContextProvider>
  );
};


export default App;
