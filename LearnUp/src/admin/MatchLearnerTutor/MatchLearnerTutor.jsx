import React, { useEffect, useState } from "react";
import axios from "axios";
import { toast } from "react-toastify";
import "./MatchLearnerTutor.css";

const MatchLearnerAndTutor = () => {
  const [tuitionRequests, setTuitionRequests] = useState([]);
  const [applications, setApplications] = useState([]);
  const [selectedTuitionID, setSelectedTuitionID] = useState(null);
  const [loading, setLoading] = useState(true); // Added loading state

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/admin/tuition-requests", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      })
      .then((response) => {
        setTuitionRequests(response.data);
        setLoading(false); // Set loading to false after data is loaded
      })
      .catch((error) => {
        console.error("Error fetching tuition requests:", error);
        setLoading(false); // Ensure loading is stopped even on error
      });
  }, []);

  const fetchApplications = (TutionID) => {
    setSelectedTuitionID(TutionID);
    axios
      .get(`http://localhost:8000/api/admin/applications/${TutionID}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      })
      .then((response) => {
        setApplications(response.data);
      })
      .catch((error) => {
        toast.error("Failed to match");
        console.error("Error fetching applications:", error);
      });
  };

  const matchTutor = (applicationID) => {
    axios.post(
        "http://localhost:8000/api/admin/match-tutor",
        { application_id: applicationID },
        { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
      ).then((response) => {
        toast.success("Tutor matched successfully!");
        fetchApplications(selectedTuitionID);
      })
      .catch((error) => {
        console.error("Error matching tutor:", error);
      });
  };

  // Show loading page when data is being fetched
  if (loading) {
    return (
      <div className="loader-container">
        <div className="loader"></div>
        <p className="loading-text">Loading...</p>
      </div>
    );
  }

  return (
    <div>
      <h2>Match Learner and Tutor</h2>

      {/* Tuition Requests Table */}
      <h3>Tuition Requests</h3>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>           
            <th>Class</th>         
            <th>Subjects</th>
            <th>Curriculum</th>
            <th>Days</th>
            <th>Location</th>
            <th>Asked Salary</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {tuitionRequests.map((request) => (
            <tr key={request.TutionID}>
              <td>{request.TutionID}</td>              
              <td>{request.class}</td>         
              <td>{request.subjects}</td>
              <td>{request.curriculum}</td>
              <td>{request.days}</td>
              <td>{request.location}</td>
              <td>{request.asked_salary}</td>
              <td>
                <button onClick={() => fetchApplications(request.TutionID)}>Applications</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Applications Table (Shown only when a tuition request is selected) */}
      {selectedTuitionID && (
        <>
          <h3>Applications for Tuition ID: {selectedTuitionID}</h3>
          <table border="1">
            <thead>
              <tr>
                <th>Application ID</th>
                <th>Tutor Name</th>
                <th>Experience</th>
                <th>Qualifications</th>
                <th>Currently Studying</th>
                <th>Preferred Salary</th>
                <th>Preferred Location</th>
                <th>Preferred Time</th>
              </tr>
            </thead>
            <tbody>
              {applications.map((app) => (
                <tr key={app.ApplicationID}>
                  <td>{app.ApplicationID}</td>
                  <td>{app.tutor_name}</td>
                  <td>{app.experience}</td>
                  <td>{app.qualification}</td>
                  <td>{app.currently_studying_in}</td>
                  <td>{app.preferred_salary}</td>
                  <td>{app.preferred_location}</td>
                  <td>{app.preferred_time}</td>
                  <td>
                    {!app.matched && (
                      <button onClick={() => matchTutor(app.ApplicationID)}>Match</button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </>
      )}
    </div>
  );
};

export default MatchLearnerAndTutor;
