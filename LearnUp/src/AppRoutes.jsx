import React from 'react'
import { Routes, Route } from "react-router-dom";
import LandingPage from './landingPage/LandingPage';
import SignIn from './signIn/SignIn';
import SignUp from './SignUp/SignUp';
import Dashboard from './Dashboard/Dashboard';
import PrivacyPolicy from "./PrivacyPolicy/PrivacyPolicy";
import FindTutors from "./FindTutors/FindTutors";
import FAQ from './FAQ/FAQ';
import TermsAndConditions from './TermsAndConditions/TermsAndConditions';

import JobBoard from './JobBoard/JobBoard';
import JobDetails from './JobDetails/JobDetails';

import AboutUs from './AboutUs/AboutUs';
import Notification from './Notification/Notification';
import AdminRoutes from './AdminRoutes';
import ProfilePage from './ProfilePage/ProfilePage';
import MyTuitions from './MyTuitions/MyTuitions';
import Inbox from './Inbox/Inbox';
import PaymentVoucher from './PaymentPage/PaymentVoucher';



const AppRoutes = () => {
  return (
    <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route path="/signIn" element={<SignIn />} />
    <Route path="/signup" element={<SignUp />} />
    <Route path="/dashboard" element={<Dashboard />} />
    <Route path="/privacy-policy" element={<PrivacyPolicy />} />
    <Route path="/AboutUs" element={<AboutUs />} />
    <Route path="/find-tutors" element={<FindTutors/>} /> 
    <Route path="/FAQ" element={<FAQ />} />
    <Route path="/TermsAndConditions" element={<TermsAndConditions />} />
    <Route path="/jobBoard" element={<JobBoard/>} /> 
    <Route path="/jobs/:id" element={<JobDetails/>} /> 
    <Route path="/notification-center" element={<Notification/>} /> 
    <Route path="/ProfilePage" element={<ProfilePage />} />
    <Route path="/myTuitions" element={<MyTuitions />} />
    <Route path="/voucher/:tutionId" element={<PaymentVoucher />} />
    
    <Route path="/admin/*" element={<AdminRoutes />} />
    <Route path="/inbox" element={<Inbox />} />
    
    
   </Routes>
  )
}

export default AppRoutes;