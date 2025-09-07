import React, { useContext, useState, useEffect } from "react";
import { storeContext } from "../context/contextProvider";
import "./MyTuitions.css";

const MyTuitions = () => {
  const { api } = useContext(storeContext); 
  const [requests, setRequests] = useState([]);
  const [editingRequest, setEditingRequest] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const { data } = await api.get("/api/tuition-requests");
        if (alive) setRequests(data);
      } catch (error) {
        console.error("Error fetching tuition requests:", error);
      } finally {
        if (alive) setLoading(false);
      }
    })();
    return () => { alive = false; };
  }, [api]);

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this request?")) return;
    try {
      await api.delete(`/api/tuition-requests/${id}`);
      setRequests((prev) => prev.filter((r) => r.TutionID !== id));
    } catch (error) {
      console.error("Error deleting tuition request:", error);
    }
  };

  const handleEditClick = (request) => setEditingRequest(request);

  const handleSaveEdit = async (e) => {
    e.preventDefault();
    try {
      await api.put(`/api/tuition-requests/${editingRequest.TutionID}`, editingRequest, {
        headers: { "Content-Type": "application/json" },
      });
      setRequests((prev) =>
        prev.map((r) => (r.TutionID === editingRequest.TutionID ? editingRequest : r))
      );
      setEditingRequest(null);
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
                    setEditingRequest((p) => ({ ...p, class: e.target.value }))
                  }
                  placeholder="Class"
                />
                <input
                  type="text"
                  value={editingRequest.subjects}
                  onChange={(e) =>
                    setEditingRequest((p) => ({ ...p, subjects: e.target.value }))
                  }
                  placeholder="Subjects"
                />
                <input
                  type="number"
                  value={editingRequest.asked_salary}
                  onChange={(e) =>
                    setEditingRequest((p) => ({ ...p, asked_salary: e.target.value }))
                  }
                  placeholder="Salary"
                />
                <input
                  type="text"
                  value={editingRequest.location}
                  onChange={(e) =>
                    setEditingRequest((p) => ({ ...p, location: e.target.value }))
                  }
                  placeholder="Location"
                />
                <input
                  type="text"
                  value={editingRequest.days}
                  onChange={(e) =>
                    setEditingRequest((p) => ({ ...p, days: e.target.value }))
                  }
                  placeholder="Days"
                />
                <div className="button-group">
                  <button type="submit" className="save-btn">Save</button>
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
                    <button className="edits-btn" onClick={() => handleEditClick(request)}>
                      
                    </button>
                    <button
                      className="delete-btn"
                      onClick={() => handleDelete(request.TutionID)}
                    >
                      
                    </button>
                  </div>
                </div>
                <p><strong>Salary:</strong> {request.asked_salary} BDT</p>
                <p><strong>Location:</strong> {request.location}</p>
                <p><strong>Days:</strong> {request.days}</p>
                <p><strong>Date:</strong> {request.updated_at}</p>
              </div>
            )
          )}
        </div>
      )}
    </div>
  );
};

export default MyTuitions;
