import React, { useContext, useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";
import "./JobDetails.css";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";

const JobDetails = () => {
  const { id } = useParams(); 
  console.log(id);
  const [job, setJob] = useState(null);
  const [loading, setLoading] = useState(true);
  const { url ,user} = useContext(storeContext);
  const handleApply=()=>{
    if (!user) {
        toast.error("Login to continue", { autoClose: 2000 }); // Show toast message
        return;
      }
      
      if (user.role !== "tutor") {
        toast.error("You are not eligible to apply", { autoClose: 2000 }); // Show toast message
        return;
      }
  
      toast.success("Applying...", { autoClose: 2000 });
  }

  useEffect(() => {
    if (!id || id.startsWith("job-")) { 
      console.error("Invalid job ID:", id);
      setLoading(false);
      return;
    }

    const fetchJobDetails = async () => {
      try {
        const response = await axios.get(`${url}/api/tuition-requests/${id}`);
        console.log("Full Job Data:", response.data);

        if (response.data) {
          setJob(response.data);
        } else {
          console.error("Job not found:", id);
        }
      } catch (error) {
        console.error("Error fetching job details:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchJobDetails();
  }, [id]);

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
      <button className="apply-btn" onClick={handleApply}>Apply Now</button>
    </div>
  );
};

export default JobDetails;
