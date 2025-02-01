import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './LandingPage/landingPage';
import SignIn from './signIn/SignIn';
import SignUp from './SignUp/SignUp';

const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route path="/signIn" element={<SignIn />} />
    <Route path="/signup" element={<SignUp />} />
    
  </Routes>
  )
}

export default AppRoutes