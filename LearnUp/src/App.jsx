import './App.css';
import { BrowserRouter as Router } from "react-router-dom";
import React from 'react';
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';
import { ContextProvider } from './context/contextProvider';

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
      </Router>
    </ContextProvider>
  );
};

export default App;
