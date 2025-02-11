import React, { useState, useContext,useEffect } from "react";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./FindTutors.css";

const FindTutors = () => {
  const { user } = useContext(storeContext);
  const navigate = useNavigate();
  const [apiBaseUrl, setApiBaseUrl] = useState("");
  useEffect(() => {
      setApiBaseUrl(import.meta.env.VITE_API_BASE_URL || "http://localhost:8000");
      
    }, []);
 
  const [formData, setFormData] = useState({
    class: "",
    subject: "",
    budget: "",
    location: "",
    schedule: "",
    curriculum: "",
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log("Submitting form..",formData);
  if (!formData.subject || !formData.class || !formData.location || !formData.budget || !formData.curriculum) {
    toast.error("Please fill all required fields!");
    return;
  }

  try {
    console.log("Sending request to:", `${apiBaseUrl}/api/tuition-requests`);
    const response = await axios.post(
      `${apiBaseUrl}/api/tuition-requests`,
      {
        ...formData,
        learner_id: user.id,
      },
      {
        headers: {
          "Content-Type": "application/json",
          
        },
       withCredentials:true,
      }
    );
    console.log("Response:", response);
    if (response.status === 200) {
      toast.success("Tuition requirement submitted successfully!");
      setFormData({
        subject: "",
        class: "",
        location: "",
        budget: "",
        schedule: "",
        curriculum: "",
      });
    } else {
      toast.error(response.data.message || "Something went wrong!");
    }
  } catch (error) {
    console.error(error);
    toast.error("Failed to submit. Try again later.");
  }
};

  return (
    <div className="find-tutors-container">
      <h2>Find a Tutor</h2>
      <form onSubmit={handleSubmit} className="find-tutors-form">
        <label>Subject: <input type="text" name="subject" value={formData.subject} onChange={handleChange} required /></label>
        <label>Grade: <input type="text" name="class" value={formData.class} onChange={handleChange} required /></label>
        <label>Location: <input type="text" name="location" value={formData.location} onChange={handleChange} required /></label>
        <label>Budget: <input type="text" name="budget" value={formData.budget} onChange={handleChange} required /></label>
        <label>Schedule: <input type="text" name="schedule" value={formData.schedule} onChange={handleChange} /></label>
        <label>Curriculum: <textarea name="curriculum" value={formData.curriculum} onChange={handleChange}></textarea></label>
        <button type="submit" onClick={() => console.log("Button clicked!")}>Submit</button>
      </form>
    </div>
  );
};

export default FindTutors;
