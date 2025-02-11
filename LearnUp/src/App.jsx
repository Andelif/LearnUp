import './App.css';
import { BrowserRouter as Router } from "react-router-dom";
import React from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';
import {ToastContainer} from "react-toastify";

const App = () => {
  return (
    <ContextProvider>

    <Router>
    <NavBar/>
    
      <AppRoutes />
  
    <Footer/>
    <ToastContainer className="toast-container" position="top-right" autoClose={3000} />
    </Router>
    </ContextProvider> 
  )
}

export default App;
