import "./Dashboard.css";
import { useContext, useState,useEffect } from "react";
import { FaEnvelope, FaCalendarAlt, FaFileInvoice } from "react-icons/fa";
import { storeContext } from "../context/contextProvider";

const Dashboard = () => {
  const [userType, setUserType] = useState("tutor");
  const {user}=useContext(storeContext);
  useEffect(() => {
    if (user?.role === "tutor") {
      setUserType("tutor");
    } else if (user?.role === "learner") {
      setUserType("learner"); 
    }
  }, [user?.role]);
  
  return (
    <div className="dashboard-container">
      
      
      
      {/* Sidebar */}
      <aside className="sidebar">
        <div className="profile-section">
          <img src="https://via.placeholder.com/80" alt="Profile" className="profile-pic" />
          <h3>{user?.name}</h3>
          <p>{user?.email}</p>
          
        </div>
      </aside>


      

      {/* Main Dashboard Content */}
      <main className="dashboard-main">

        
       

        <div className="stats-container">
          <div className="stat-box"> 
            <h2>25</h2> 
            <p>{userType === "tutor" ? "Applied Jobs" : "Applied Requests"}</p> 
          </div>
          <div className="stat-box"> <h2>10</h2> <p>{userType === "tutor" ? "Shortlisted Jobs" : "Shortlisted Tutors"}</p> </div>
          <div className="stat-box"> <h2>2</h2> <p>{userType === "tutor" ? "Appointed Jobs" : "Appointed Tutors"}</p> </div>
          <div className="stat-box"> <h2>3</h2> <p>{userType === "tutor" ? "Confirmed Jobs" : "Confirmed Tutors"}</p> </div>
          <div className="stat-box"> <h2>9</h2> <p>{userType === "tutor" ? "Cancelled Jobs" : "Cancelled Tutors"}</p> </div>
        </div>

        <div className="notice-board">
          <h3>Notice Board</h3>
          <p>"Tutor of the Month, December 2024" is Md. Abidur Rahman...</p>
        </div>

        <div className="info-boxes">
          <div className="info-box">
            <FaCalendarAlt /> <p>Member Since: Aug 21, 2022</p>
          </div>
          <div className="info-box">
            <FaEnvelope /> <p>03 Confirmation Letters</p>
          </div>
          <div className="info-box">
            <FaFileInvoice /> <p>01 Invoice Pending</p>
          </div>
        </div>
      </main>
    </div>
  );
};

export default Dashboard;
