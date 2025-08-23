import React, { useEffect, useState, useContext } from "react";
import { Link } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import "./JobBoard.css";

const JobBoard = () => {
  const { api } = useContext(storeContext);
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);

  // Filters
  const [searchSub, setSearchSub] = useState("");
  const [searchLocation, setSearchLocation] = useState("");
  const [searchClass, setSearchClass] = useState("");

  useEffect(() => {
    (async () => {
      try {
        const { data } = await api.get("/api/tuition-requests/all");
        setJobs(Array.isArray(data) ? data : []);
      } catch (error) {
        console.error("Error fetching jobs:", error);
        setJobs([]);
      } finally {
        setLoading(false);
      }
    })();
  }, [api]);

  // Filtering logic
  const filteredJobs = jobs.filter((job) => {
    const subj = (job.subjects || job.subject || "").toLowerCase();
    const loc  = (job.location || "").toLowerCase();
    const cls  = (job.class || "").toLowerCase();

    return (
      (searchSub ? subj.includes(searchSub.toLowerCase()) : true) &&
      (searchLocation ? loc.includes(searchLocation.toLowerCase()) : true) &&
      (searchClass ? cls.includes(searchClass.toLowerCase()) : true)
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
        <input
          type="text"
          value={searchSub}
          onChange={(e) => setSearchSub(e.target.value)}
          placeholder="Subject"
        />
        <input
          type="text"
          value={searchLocation}
          onChange={(e) => setSearchLocation(e.target.value)}
          placeholder="Location"
        />
        <input
          type="text"
          value={searchClass}
          onChange={(e) => setSearchClass(e.target.value)}
          placeholder="Class"
        />
      </div>

      {/* Job List */}
      <div className="job-list">
        {filteredJobs.length === 0 ? (
          <p>No matching tuition jobs found.</p>
        ) : (
          filteredJobs.map((job) => (
            <div key={job.TutionID} className="job-card">
              <h3>{job.class} - {job.subjects || job.subject}</h3>
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
