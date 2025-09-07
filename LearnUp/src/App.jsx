import './App.css';
import { BrowserRouter as Router, useLocation } from "react-router-dom";
import React, { useContext } from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';
import { storeContext } from './context/contextProvider';
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import AdminSidebar from './admin/AdminSidebar/AdminSidebar';

const AppContentInner = () => {
  const { user } = useContext(storeContext);
  const location = useLocation();
  const isAdmin = user?.role === "admin";
  const isHomePage = location.pathname === "/";
  
  // Don't show sidebar for admin on home page
  const shouldShowSidebar = isAdmin && !isHomePage;

  return (
    <div className="app-container">
      <NavBar />
      <div className="main-content">
        {shouldShowSidebar && <AdminSidebar />}
        <AppRoutes />
      </div>
      {!isAdmin && <Footer />}
    </div>
  );
};

const AppContent = () => {
  return (
    <Router>
      <AppContentInner />
      <ToastContainer
        position="top-right"
        autoClose={3000}
        hideProgressBar={false}
        closeOnClick
        pauseOnHover
        draggable
        theme="colored"
        limit={3}
        newestOnTop={true}
        rtl={false}
        pauseOnFocusLoss={false}
        style={{
          zIndex: 10000,
          position: 'fixed',
          top: '100px',
          right: '20px'
        }}
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
