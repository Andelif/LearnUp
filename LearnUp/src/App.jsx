import './App.css'
import {BrowserRouter as Router} from "react-router-dom";
import React from 'react'
import AppRoutes from './AppRoutes';
import NavBar from './NavBar/NavBar';
import Footer from './Footer/Footer';

const App = () => {
  return (
    <Router>
    <NavBar/>
    
      <AppRoutes />
  
    <Footer/>
    </Router> 
  )
}

export default App