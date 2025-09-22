import React, { useContext, useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import "./JobDetails.css";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";

const JobDetails = () => {
  const { id } = useParams();
  const { api, user } = useContext(storeContext);

  const [job, setJob] = useState(null);
  const [hasApplied, setHasApplied] = useState(false);
  const [loading, setLoading] = useState(true);

  const handleApply = async () => {
    if (!user) {
      toast.error("Login to continue", { autoClose: 2000 });
      return;
    }
    if (user.role !== "tutor") {
      toast.error("You are not eligible to apply", { autoClose: 2000 });
      return;
    }
    if (hasApplied) {
      toast.error("You have already applied for this job");
      return;
    }

    try {
      const { data } = await api.post("/api/applications", {
        tution_id: id,
      });
      toast.success(data.message || "Applied successfully", { autoClose: 2000 });
      setHasApplied(true);
    } catch (error) {
      if (error.response) {
        toast.error(error.response.data?.message || "Failed to apply", { autoClose: 2000 });
      } else {
        toast.error("Network error. Please try again later.", { autoClose: 2000 });
      }
    }
  };

  useEffect(() => {
    if (!id || id.startsWith("job-")) {
      console.error("Invalid job ID:", id);
      setLoading(false);
      return;
    }

    const fetchJobDetails = async () => {
      try {
        const { data } = await api.get(`/api/tuition-requests/${id}`);
        setJob(data || null);
      } catch (error) {
        console.error("Error fetching job details:", error);
      } finally {
        setLoading(false);
      }
    };

    const checkApplicationStatus = async () => {
      if (!user) return;
      try {
        const { data } = await api.get(`/api/applications/check/${id}`);
        if (data?.applied) setHasApplied(true);
      } catch (error) {
        console.error("Error checking application status:", error);
      }
    };

    fetchJobDetails();
    if (user) checkApplicationStatus();
  }, [id, user, api]);

  if (loading) return (
    <div className="job-details-loading">
      <div className="loading-spinner"></div>
      <p>Loading job details...</p>
    </div>
  );
  
  if (!job) return (
    <div className="job-details-error">
      <h2>Job Not Found</h2>
      <p>The job you're looking for doesn't exist or has been removed.</p>
    </div>
  );

  return (
    <div className="job-details-container">
      <div className="job-card">
        <div className="job-header">
          <h2 className="job-title">{job.subject || job.subjects} Tutor Needed</h2>
          <p className="job-subtitle">{job.class} | {job.curriculum}</p>
        </div>

        <div className="job-info-grid">
          <div className="info-item">
            <div className="info-icon">📚</div>
            <div className="info-content">
              <label>Subjects</label>
              <span>{job.subjects}</span>
            </div>
          </div>
          
          <div className="info-item">
            <div className="info-icon">📍</div>
            <div className="info-content">
              <label>Location</label>
              <span>{job.location || "Not specified"}</span>
            </div>
          </div>
          
          <div className="info-item">
            <div className="info-icon">💰</div>
            <div className="info-content">
              <label>Salary</label>
              <span>{job.asked_salary} BDT</span>
            </div>
          </div>
          
          <div className="info-item">
            <div className="info-icon">📅</div>
            <div className="info-content">
              <label>Days per Week</label>
              <span>{job.days || "Flexible"}</span>
            </div>
          </div>
        </div>

        {user?.role !== "learner" && (
          <div className="apply-section">
            <button
              className={`apply-btn ${hasApplied ? "applied" : ""}`}
              onClick={handleApply}
              disabled={hasApplied}
            >
              {hasApplied ? (
                <>
                  <span className="btn-icon">✓</span>
                  Applied Successfully
                </>
              ) : (
                <>
                  <span className="btn-icon">✏️</span>
                  Apply Now
                </>
              )}
            </button>
            {hasApplied && (
              <p className="application-note">Your application has been submitted successfully!</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default JobDetails;