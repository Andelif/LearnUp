import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";
import "./JobBoard.css";

const JobBoard = () => {
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);

  // Filters
  const [searchDate, setSearchDate] = useState("");
  const [searchLocation, setSearchLocation] = useState("");
  const [searchClass, setSearchClass] = useState("");

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/tuition-requests")
      .then((response) => {
        setJobs(response.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching jobs:", error);
        setLoading(false);
      });
  }, []);

  // Filtering logic
  const filteredJobs = jobs.filter((job) => {
    return (
      // (searchDate ? (job.date?.includes(searchDate) || false) : true) &&
      (searchLocation ? (job.location?.toLowerCase().includes(searchLocation.toLowerCase()) || false) : true) &&
      (searchClass ? (job.class?.toLowerCase().includes(searchClass.toLowerCase()) || false) : true)
    );
  });

  if (loading) return <p>Loading jobs...</p>;

  return (
    <div className="job-board">
      <h1 className="title">Available Tuition Jobs</h1>

      {/* Filters */}
      <div className="filter-container">
        {/* <input type="date" value={searchDate} onChange={(e) => setSearchDate(e.target.value)} placeholder="Date" /> */}
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
              <Link to={`/jobs/${job.id}`} className="details-button">View Details</Link>
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default JobBoard;
