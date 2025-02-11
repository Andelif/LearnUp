import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './landingPage/LandingPage';
import SignIn from './signIn/SignIn';
import SignUp from './SignUp/SignUp';
import Dashboard from './Dashboard/Dashboard';
import PrivacyPolicy from "./PrivacyPolicy/PrivacyPolicy";
import FAQ from './FAQ/FAQ';
import TermsAndConditions from './TermsAndConditions/TermsAndConditions';

const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route path="/signIn" element={<SignIn />} />
    <Route path="/signup" element={<SignUp />} />
    <Route path="/dashboard" element={<Dashboard />} />
    <Route path="/privacy-policy" element={<PrivacyPolicy />} />
    <Route path="/FAQ" element={<FAQ />} />
    <Route path="/TermsAndConditions" element={<TermsAndConditions />} />
    
    

    
  </Routes>
  )
}

export default AppRoutes;