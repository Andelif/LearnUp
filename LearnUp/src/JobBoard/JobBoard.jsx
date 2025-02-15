import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";
import "./JobBoard.css";

const JobBoard = () => {
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    axios.get("http://localhost:8000/api/tuition-requests") 
      .then((response) => {
        setJobs(response.data);
        setLoading(false);
        console.log("API Response:", response.data);
      })
      .catch((error) => {
        console.error("Error fetching jobs:", error);
        setLoading(false);
      });
  }, []);

  if (loading) return <p>Loading jobs...</p>;

  return (
    
      <div className="job-board">
        <h1 className="title">Available Tuition Jobs</h1>
        <div className="job-list">
          {jobs.length === 0 ? (
            <p>No tuition jobs available.</p>
          ) : (
            jobs.map((job, index) => {
              const jobId = job.TutionID || `job-${index}`; // Ensure a valid ID
              console.log(jobId)
              return (
                <div key={jobId} className="job-card">
                  <h3>{job.class} - {job.subjects}</h3>
                  <p><strong>Salary:</strong> {job.asked_salary} BDT</p>
                  <p><strong>Location:</strong> {job.location}</p>
                  <p><strong>Days:</strong> {job.days}</p>
                  <Link to={`/jobs/${jobId}`} className="details-button">
                    View Details
                  </Link>
                </div>
              );
            })
          )}
        </div>
      </div>
    );
};

export default JobBoard;
