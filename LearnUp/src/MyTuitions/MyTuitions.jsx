import React, { useContext, useState, useEffect } from "react";
import axios from "axios";
import { storeContext } from "../context/contextProvider";
import "./MyTuitions.css"; // Corrected import

const MyTuitions = () => {
  const [requests, setRequests] = useState([]);
  const [editingRequest, setEditingRequest] = useState(null);
  const [loading, setLoading] = useState(true); // Loading state
  const { token } = useContext(storeContext);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/tuition-requests", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        setRequests(response.data);
        setLoading(false); // Stop loading
      })
      .catch((error) => {
        console.error("Error fetching tuition requests:", error);
        setLoading(false); // Stop loading in case of error
      });
  }, [token]);

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

  const handleEditClick = (request) => {
    setEditingRequest(request);
  };

  const handleSaveEdit = async (e) => {
    e.preventDefault();

    try {
      await axios.put(
        `http://localhost:8000/api/tuition-requests/${editingRequest.TutionID}`,
        editingRequest,
        {
          headers: { Authorization: `Bearer ${token}` },
        }
      );

      setRequests(
        requests.map((request) =>
          request.TutionID === editingRequest.TutionID ? editingRequest : request
        )
      );

      setEditingRequest(null); // Exit edit mode
    } catch (error) {
      console.error("Error updating tuition request:", error);
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
    <div className="tuition-container">
      <h1>All Tuition Requests</h1>
      {requests.length === 0 ? (
        <p>No tuition requests found.</p>
      ) : (
        <div className="tuition-grid">
          {requests.map((request) =>
            editingRequest && editingRequest.TutionID === request.TutionID ? (
              <form
                key={request.TutionID}
                className="tuition-card edit-form"
                onSubmit={handleSaveEdit}
              >
                <input
                  type="text"
                  value={editingRequest.class}
                  onChange={(e) =>
                    setEditingRequest((prevState) => ({
                      ...prevState,
                      class: e.target.value,
                    }))
                  }
                  placeholder="Class"
                />
                <input
                  type="text"
                  value={editingRequest.subjects}
                  onChange={(e) =>
                    setEditingRequest((prevState) => ({
                      ...prevState,
                      subjects: e.target.value,
                    }))
                  }
                  placeholder="Subjects"
                />
                <input
                  type="number"
                  value={editingRequest.asked_salary}
                  onChange={(e) =>
                    setEditingRequest((prevState) => ({
                      ...prevState,
                      asked_salary: e.target.value,
                    }))
                  }
                  placeholder="Salary"
                />
                <input
                  type="text"
                  value={editingRequest.location}
                  onChange={(e) =>
                    setEditingRequest((prevState) => ({
                      ...prevState,
                      location: e.target.value,
                    }))
                  }
                  placeholder="Location"
                />
                <input
                  type="text"
                  value={editingRequest.days}
                  onChange={(e) =>
                    setEditingRequest((prevState) => ({
                      ...prevState,
                      days: e.target.value,
                    }))
                  }
                  placeholder="Days"
                />
                <div className="button-group">
                  <button type="submit" className="save-btn">
                    Save
                  </button>
                  <button
                    type="button"
                    className="cancel-btn"
                    onClick={() => setEditingRequest(null)}
                  >
                    Cancel
                  </button>
                </div>
              </form>
            ) : (
              <div key={request.TutionID} className="tuition-card">
                <div className="tuition-header">
                  <h3 className="tuition-title">
                    {request.class} - {request.subjects}
                  </h3>
                  <div className="button-group">
                    <button
                      className="edit-btn"
                      onClick={() => handleEditClick(request)}
                    >
                      Edit
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(request.TutionID)}
                    >
                      Delete
                    </button>
                  </div>
                </div>
                <p>
                  <strong>Salary:</strong> {request.asked_salary} BDT
                </p>
                <p>
                  <strong>Location:</strong> {request.location}
                </p>
                <p>
                  <strong>Days:</strong> {request.days}
                </p>
                <p>
                  <strong>Date:</strong> {request.updated_at}
                </p>
                
              </div>
            )
          )}
        </div>
      )}
    </div>
  );
};

export default MyTuitions;
