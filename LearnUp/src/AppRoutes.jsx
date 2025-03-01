import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './LandingPage/landingPage';
import SignIn from './signIn/SignIn';
import SignUp from './SignUp/SignUp';
import Dashboard from './Dashboard/Dashboard';

const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route path="/signIn" element={<SignIn />} />
    <Route path="/signup" element={<SignUp />} />
    <Route path="/dashboard" element={<Dashboard />} />
    
  </Routes>
  )
}

export default AppRoutes;