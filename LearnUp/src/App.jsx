import './App.css';
import { BrowserRouter as Router } from "react-router-dom";
import React from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

const App = () => {
  return (
    <ContextProvider>
      <Router>
        <div className="app-container">
          <NavBar />
          <div className="main-content">
            <AppRoutes />
          </div>
          <Footer />
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
