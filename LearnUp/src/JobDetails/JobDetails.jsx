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
  const [hasApplied, setHasApplied] = useState(false);
  const [loading, setLoading] = useState(true);
  const { url ,user} = useContext(storeContext);
  const handleApply=async ()=>{
    if (!user) {
        toast.error("Login to continue", { autoClose: 2000 }); // Show toast message
        return;
      }
      
      if (user.role !== "tutor") {
        toast.error("You are not eligible to apply", { autoClose: 2000 }); // Show toast message
        return;
      }
      if(hasApplied){
        toast.error("You have already applied for this job");
        return;
      }
  
      try {
        const response = await axios.post(`${url}/api/applications`, {
          tution_id: id, // Send the job ID as tuition_id
          
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`, // Include token
            "Content-Type": "application/json",
          },
        });
    
        toast.success(response.data.message, { autoClose: 2000 });
        setHasApplied(true);
      } catch (error) {
        if (error.response) {
          toast.error(error.response.data.message || "Failed to apply", { autoClose: 2000 });
        } else {
          toast.error("Network error. Please try again later.", { autoClose: 2000 });
        }
      }
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
    const checkApplicationStatus = async () => {
      if (!user) return;

      try {
        const res = await axios.get(`${url}/api/applications/check/${id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (res.data.applied) {
          setHasApplied(true);
        }
      } catch (error) {
        console.error("Error checking application status:", error);
      }
    };

    fetchJobDetails();
    if (user) checkApplicationStatus();
  }, [id, user]);

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
      <button className="apply-btn" onClick={handleApply} disabled={hasApplied}>{hasApplied ? "Applied" : "Apply Now"}</button>
    </div>
  );
};

export default JobDetails;
