import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './landingPage/LandingPage';
import SignIn from './signIn/SignIn';
import SignUp from './SignUp/SignUp';
import Dashboard from './Dashboard/Dashboard';
import PrivacyPolicy from "./PrivacyPolicy/PrivacyPolicy";

import FindTutors from "./FindTutors/FindTutors";
import FAQ from './FAQ/FAQ';


const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route path="/signIn" element={<SignIn />} />
    <Route path="/signup" element={<SignUp />} />
    <Route path="/dashboard" element={<Dashboard />} />
    <Route path="/privacy-policy" element={<PrivacyPolicy />} />

    {/* <Route path="/find-tutors" element={<FindTutors/>} /> */}

    <Route path="/FAQ" element={<FAQ />} />
   </Routes>
  )
}

export default AppRoutes;