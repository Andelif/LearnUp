import React, { useContext, useState, useEffect } from "react";
import axios from "axios";
import { storeContext } from "../context/contextProvider";
import "./MyTuitions"; // Import CSS file

const MyTuitions = () => {
  const [requests, setRequests] = useState([]);
  const { token } = useContext(storeContext);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/tuition-requests", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => setRequests(response.data))
      .catch((error) =>
        console.error("Error fetching tuition requests:", error)
      );
  }, []);

  // Handle Delete Request
  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this request?")) return;
    
    try {
      await axios.delete(`http://localhost:8000/api/tuition-requests/${id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });

      setRequests(requests.filter((request) => request.TutionID !== id));
    } catch (error) {
      console.error("Error deleting tuition request:", error);
    }
  };

  // Handle Edit Request
  const handleEdit = (id) => {
    alert(`Edit functionality for tuition request ID: ${id} will be added soon!`);
    // You can implement an edit modal or navigate to an edit page.
  };

  return (
    <div className="tuition-container">
      <h2>All Tuition Requests</h2>
      {requests.length === 0 ? (
        <p>No tuition requests found.</p>
      ) : (
        <div className="tuition-grid">
          {requests.map((request) => (
            <div key={request.TutionID} className="tuition-card">
              <div className="tuition-header">
                <h3 className="tuition-title">{request.class} - {request.subjects}</h3>
                <div className="button-group">
                  <button className="edit-btn" onClick={() => handleEdit(request.TutionID)}>Edit</button>
                  <button className="delete-btn" onClick={() => handleDelete(request.TutionID)}>Delete</button>
                </div>
              </div>
              <p><strong>Salary:</strong> {request.asked_salary} BDT</p>
              <p><strong>Location:</strong> {request.location}</p>
              <p><strong>Days:</strong> {request.days}</p>
              <p><strong>Date:</strong> {request.updated_at}</p>
              <p><strong>Posted by:</strong> {request.learner_name} ({request.learner_email})</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default MyTuitions;
