import React, { useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./FindTutors.css";

const FindTutors = () => {
  const { user, token, url } = useContext(storeContext);
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    class: "",
    subjects: "",
    asked_salary: "",
    location: "",
    days: "",
    curriculum: "",
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log("Submitting form with token: ", formData, token);

    // Check if all fields are filled
    if (!formData.subjects || !formData.class || !formData.location || !formData.asked_salary || !formData.curriculum || !formData.days) {
      toast.error("Please fill all required fields!");
      return;
    }

    // Ensure the token exists before sending the request
    if (!token) {
      toast.error("Unauthorized! Please log in first.");
      navigate("/signin");
      return;
    }

    try {
      console.log("Sending request to:", `${url}/api/tuition-requests`);
      const response = await axios.post(
        `${url}/api/tuition-requests`,
        {
          ...formData,
          learner_id: user.id,
        },
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`, // Include token
          },
        }
      );

      console.log("Response:", response);
      if (response.status === 201) {
        toast.success("Tuition requirement submitted successfully!");
        setFormData({
          subjects: "",
          class: "",
          location: "",
          asked_salary: "",
          days: "",
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
        <label>Subject: <input type="text" name="subjects" value={formData.subjects} onChange={handleChange} required /></label>
        <label>Grade: <input type="text" name="class" value={formData.class} onChange={handleChange} required /></label>
        <label>Location: <input type="text" name="location" value={formData.location} onChange={handleChange} required /></label>
        <label>Budget: <input type="text" name="asked_salary" value={formData.asked_salary} onChange={handleChange} required /></label>
        <label>Schedule: <input type="text" name="days" value={formData.days} onChange={handleChange} required /></label>
        <label>Curriculum: <textarea name="curriculum" value={formData.curriculum} onChange={handleChange} required></textarea></label>
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default FindTutors;
