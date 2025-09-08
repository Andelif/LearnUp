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
      // backend expects the body field named tuition_id (check your controller);
      // if it actually expects tution_id, keep it as below.
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

  if (loading) return <p>Loading job details...</p>;
  if (!job) return <p>Job not found</p>;

  return (
    <div className="job-details-container">
      <h2>{job.subject} Tutor Needed</h2>
      <p><strong>Class:</strong> {job.class}</p>
      <p><strong>Subjects:</strong> {job.subjects}</p>
      <p><strong>Location:</strong> {job.location}</p>
      <p><strong>Salary:</strong> {job.asked_salary} BDT</p>
      <p><strong>Curriculum:</strong> {job.curriculum}</p>
      <p><strong>Days per Week:</strong> {job.days}</p>
      {user?.role !== "learner" && (
      <button
        className="apply-btn"
        onClick={handleApply}
        disabled={hasApplied}
      >
        {hasApplied ? "Applied" : "Apply Now"}
      </button>
    )}
    </div>
  );
};

export default JobDetails;
