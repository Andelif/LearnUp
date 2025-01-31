import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './LandingPage/landingPage';

const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    
  </Routes>
  )
}

export default AppRoutes