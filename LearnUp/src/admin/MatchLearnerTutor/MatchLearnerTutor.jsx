import React, { useEffect, useState, useContext } from "react";
import { storeContext } from "../../context/contextProvider";
import { toast } from "react-toastify";
import "./MatchLearnerTutor.css";

const MatchLearnerAndTutor = () => {
  const { api } = useContext(storeContext);

  const [tuitionRequests, setTuitionRequests] = useState([]);
  const [applications, setApplications] = useState([]);
  const [selectedTuitionID, setSelectedTuitionID] = useState(null);
  const [loading, setLoading] = useState(true);
  const [matchingId, setMatchingId] = useState(null); // disable one button while posting

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const { data } = await api.get("/api/admin/tuition-requests");
        if (alive) setTuitionRequests(Array.isArray(data) ? data : []);
      } catch (error) {
        console.error("Error fetching tuition requests:", error);
        toast.error("Failed to load tuition requests");
      } finally {
        if (alive) setLoading(false);
      }
    })();
    return () => {
      alive = false;
    };
  }, [api]);

  const fetchApplications = async (TutionID) => {
    setSelectedTuitionID(TutionID);
    try {
      const { data } = await api.get(`/api/admin/applications/${TutionID}`);
      setApplications(Array.isArray(data) ? data : []);
    } catch (error) {
      toast.error("Failed to load applications");
      console.error("Error fetching applications:", error);
    }
  };

  const matchTutor = async (applicationId) => {
    try {
      setMatchingId(applicationId);
      await api.post("/api/admin/match-tutor", { application_id: applicationId });
      toast.success("Tutor matched successfully!");
      if (selectedTuitionID) await fetchApplications(selectedTuitionID);
    } catch (error) {
      const msg =
        error?.response?.data?.error ||
        error?.response?.data?.message ||
        "Match failed";
      console.error("Error matching tutor:", error);
      toast.error(msg);
    } finally {
      setMatchingId(null);
    }
  };

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

    
    <h3>Tuition Requests</h3>
    <div className="table-scroll">
      <table>
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
                <button onClick={() => fetchApplications(request.TutionID)}>
                  Applications
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>

    
    {selectedTuitionID && (
      <>
        <h3>Applications for Tuition ID: {selectedTuitionID}</h3>
        <div className="table-scroll">
          <table>
            <thead>
              <tr>
                <th>Application ID</th>
                <th>Tutor Name</th>
                <th>Experience</th>
                <th>Qualification</th>
                <th>Currently Studying</th>
                <th>Preferred Salary</th>
                <th>Preferred Location</th>
                <th>Preferred Time</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {applications.map((app) => (
                <tr key={app.application_id}>
                  <td>{app.application_id}</td>
                  <td>{app.tutor_name}</td>
                  <td>{app.experience}</td>
                  <td>{app.qualification}</td>
                  <td>{app.currently_studying_in}</td>
                  <td>{app.preferred_salary}</td>
                  <td>{app.preferred_location}</td>
                  <td>{app.preferred_time}</td>
                  <td>
                    {!app.matched && (
                      <button
                        onClick={() => matchTutor(app.application_id)}
                        disabled={matchingId === app.application_id}
                      >
                        {matchingId === app.application_id ? "Matching..." : "Match"}
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </>
    )}
  </div>
);
};

export default MatchLearnerAndTutor;
