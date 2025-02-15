import './App.css';
import { BrowserRouter as Router } from "react-router-dom";
import React from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';
import { ToastContainer } from "react-toastify";

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
        <ToastContainer className="toast-container"
          position="top-center"
          autoClose={2000} // Toast disappears in 2 seconds
          hideProgressBar={true}
          closeOnClick
          pauseOnHover={false}
          draggable={false}  />
      </Router>
    </ContextProvider>
  );
}

export default App;
