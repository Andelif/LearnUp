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

const App = () => {
  const {user}=useContext(storeContext);
  const isAdmin= user?.role === "admin";
  return (
    <ContextProvider>
      <Router>
        <div className="app-container">
          <NavBar />
          <div className="main-content">
            {isAdmin && <AdminSidebar/>}
            <AppRoutes />
          </div>
          {!isAdmin && <Footer />}
        </div>
        <ToastContainer
          position="top-right" // More conventional position
          autoClose={3000} // Toast disappears in 3 seconds
          hideProgressBar={false}
          closeOnClick
          pauseOnHover
          draggable
          theme="colored" // Try "light" or "dark" if needed
        />
      </Router>
    </ContextProvider>
  );
}

export default App;
