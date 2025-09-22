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

  const handleEditClick = (request) => setEditingRequest({...request});

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

  const handleCancelEdit = () => setEditingRequest(null);

  const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };

  if (loading) {
    return (
      <div className="loader-container">
        <div className="loader"></div>
        <p className="loading-text">Loading your tuition requests...</p>
      </div>
    );
  }

  return (
    <div className="tuition-container">
      <div className="page-header">
        <h1>My Tuition Requests</h1>
        <p className="page-subtitle">Manage your posted tuition requests</p>
      </div>
      
      {requests.length === 0 ? (
        <div className="empty-state">
          <div className="empty-icon">📚</div>
          <h3>No tuition requests yet</h3>
          <p>You haven't posted any tuition requests. Create one to get started!</p>
        </div>
      ) : (
        <div className="tuition-grid">
          {requests.map((request) => (
            <div key={request.TutionID} className="tuition-card">
              {editingRequest && editingRequest.TutionID === request.TutionID ? (
                <form className="edit-form" onSubmit={handleSaveEdit}>
                  <h3 className="edit-title">Edit Tuition Request</h3>
                  
                  <div className="form-group">
                    <label>Class/Grade</label>
                    <input
                      type="text"
                      value={editingRequest.class}
                      onChange={(e) =>
                        setEditingRequest((p) => ({ ...p, class: e.target.value }))
                      }
                      placeholder="e.g., Grade 5, Class 10"
                    />
                  </div>
                  
                  <div className="form-group">
                    <label>Subjects</label>
                    <input
                      type="text"
                      value={editingRequest.subjects}
                      onChange={(e) =>
                        setEditingRequest((p) => ({ ...p, subjects: e.target.value }))
                      }
                      placeholder="e.g., Math, Physics, English"
                    />
                  </div>
                  
                  <div className="form-row">
                    <div className="form-group">
                      <label>Salary (BDT)</label>
                      <input
                        type="number"
                        value={editingRequest.asked_salary}
                        onChange={(e) =>
                          setEditingRequest((p) => ({ ...p, asked_salary: e.target.value }))
                        }
                        placeholder="Expected salary"
                      />
                    </div>
                    
                    <div className="form-group">
                      <label>Curriculum</label>
                      <input
                        type="text"
                        value={editingRequest.curriculum || ""}
                        onChange={(e) =>
                          setEditingRequest((p) => ({ ...p, curriculum: e.target.value }))
                        }
                        placeholder="e.g., National, Cambridge"
                      />
                    </div>
                  </div>
                  
                  <div className="form-group">
                    <label>Location</label>
                    <input
                      type="text"
                      value={editingRequest.location || ""}
                      onChange={(e) =>
                        setEditingRequest((p) => ({ ...p, location: e.target.value }))
                      }
                      placeholder="e.g., Mirpur, Dhaka"
                    />
                  </div>
                  
                  <div className="form-group">
                    <label>Days per week</label>
                    <input
                      type="text"
                      value={editingRequest.days || ""}
                      onChange={(e) =>
                        setEditingRequest((p) => ({ ...p, days: e.target.value }))
                      }
                      placeholder="e.g., Sat, Mon, Wed"
                    />
                  </div>
                  
                  <div className="form-actions">
                    <button type="submit" className="btn-primary">
                      Save Changes
                    </button>
                    <button type="button" className="btn-secondary" onClick={handleCancelEdit}>
                      Cancel
                    </button>
                  </div>
                </form>
              ) : (
                <>
                  <div className="card-header">
                    <div className="class-badge">{request.class}</div>
                    <div className="salary-amount">{request.asked_salary} BDT</div>
                  </div>
                  
                  <h3 className="subject-title">{request.subjects}</h3>
                  
                  <div className="card-details">
                    <div className="detail-item">
                      <span className="detail-icon">📍</span>
                      <span className="detail-text">{request.location || "Not specified"}</span>
                    </div>
                    
                    <div className="detail-item">
                      <span className="detail-icon">📅</span>
                      <span className="detail-text">{request.days || "Flexible"}</span>
                    </div>
                    
                    {request.curriculum && (
                      <div className="detail-item">
                        <span className="detail-icon">📚</span>
                        <span className="detail-text">{request.curriculum}</span>
                      </div>
                    )}
                    
                    <div className="detail-item">
                      <span className="detail-icon">🕒</span>
                      <span className="detail-text">Posted {formatDate(request.updated_at)}</span>
                    </div>
                  </div>
                  
                  <div className="card-actions">
                    <button 
                      className="action-btn edit-btn"
                      onClick={() => handleEditClick(request)}
                      title="Edit request"
                    >
                      <span className="btn-icon">✏️</span>
                      Edit
                    </button>
                    <button 
                      className="action-btn delete-btn"
                      onClick={() => handleDelete(request.TutionID)}
                      title="Delete request"
                    >
                      <span className="btn-icon">🗑️</span>
                      Delete
                    </button>
                  </div>
                </>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default MyTuitions;