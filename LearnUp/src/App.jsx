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
          { <Footer />}
        </div>

        <ToastContainer
          position="bottom-center" 
          autoClose={3000} 
          hideProgressBar={false}
          closeOnClick
          pauseOnHover
          draggable
          theme="colored" 
        />
        
      </Router>
    </ContextProvider>
  );
}

export default App;
