import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";
import "./JobBoard.css";

const JobBoard = () => {
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);

  // Filters
  const [searchSub, setSearchSub] = useState("");
  const [searchLocation, setSearchLocation] = useState("");
  const [searchClass, setSearchClass] = useState("");

  useEffect(() => {
    const token = localStorage.getItem("token"); // Retrieve token
    axios
      .get("http://localhost:8000/api/tuition-requests/all")
      .then((response) => {
        console.log("API Response:", response.data); // Debugging
        if (Array.isArray(response.data)) {
          setJobs(response.data);
        } else {
          setJobs([]); // Fallback to empty array if response is not an array
        }
        setLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching jobs:", error);
        setJobs([]); // Prevents the filter error
        setLoading(false);
      });
  }, []);
  


  // Filtering logic
  const filteredJobs = jobs.filter((job) => {
    return (
      (searchSub ? (job.subjects?.toLowerCase().includes(searchSub.toLowerCase()) || false) : true) &&
      (searchLocation ? (job.location?.toLowerCase().includes(searchLocation.toLowerCase()) || false) : true) &&
      (searchClass ? (job.class?.toLowerCase().includes(searchClass.toLowerCase()) || false) : true)
    );
  });

  if (loading) {
    return (
      <div className="loader-container">
        <div className="loader"></div>
        <p className="loading-text">Loading...</p>
      </div>
    );
  }
  
  return (
    <div className="job-board">
      <h1 className="title">Available Tuition Jobs</h1>

      {/* Filters */}
      <div className="filter-container">
        <input type="text" value={searchSub} onChange={(e) => setSearchSub(e.target.value)} placeholder="Subject" />
        <input type="text" value={searchLocation} onChange={(e) => setSearchLocation(e.target.value)} placeholder="Location" />
        <input type="text" value={searchClass} onChange={(e) => setSearchClass(e.target.value)} placeholder="Class" />
      </div>

      {/* Job List */}
      <div className="job-list">
        {filteredJobs.length === 0 ? (
          <p>No matching tuition jobs found.</p>
        ) : (
          filteredJobs.map((job) => (
            <div key={job.TutionID} className="job-card">
              <h3>{job.class} - {job.subjects}</h3>
              <p><strong>Salary:</strong> {job.asked_salary} BDT</p>
              <p><strong>Location:</strong> {job.location}</p>
              <p><strong>Days:</strong> {job.days}</p>
              <p><strong>Date:</strong> {job.updated_at}</p>
              <Link to={`/jobs/${job.TutionID}`} className="details-button">View Details</Link>
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default JobBoard;