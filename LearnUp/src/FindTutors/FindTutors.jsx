import React, { useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
import "./FindTutors.css";

const FindTutors = () => {
  const { api, user, token } = useContext(storeContext);
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    class: "",
    subjects: "",
    asked_salary: "",
    location: "",
    days: "",
    curriculum: "",
  });

  const handleChange = (e) =>
    setFormData({ ...formData, [e.target.name]: e.target.value });

  const handleSubmit = async (e) => {
    e.preventDefault();

    // required field check
    if (
      !formData.subjects ||
      !formData.class ||
      !formData.location ||
      !formData.asked_salary ||
      !formData.curriculum ||
      !formData.days
    ) {
      toast.error("Please fill all required fields!");
      return;
    }

    // must be logged in
    if (!token) {
      toast.error("Unauthorized! Please log in first.");
      navigate("/signIn");
      return;
    }

    try {
      const res = await api.post("/api/tuition-requests", {
        ...formData,
        learner_id: user?.id, // keep if your backend expects it
      });

      if (res.status === 201) {
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
        toast.error(res.data?.message || "Something went wrong!");
      }
    } catch (err) {
      console.error(err);
      toast.error("Failed to submit. Try again later.");
    }
  };

  return (
    <div className="find-tutors-container">
      <h2>Find a Tutor</h2>
      <form onSubmit={handleSubmit} className="find-tutors-form">
        <label>
          Subject:
          <input
            type="text"
            name="subjects"
            value={formData.subjects}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Grade:
          <input
            type="text"
            name="class"
            value={formData.class}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Location:
          <input
            type="text"
            name="location"
            value={formData.location}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Budget:
          <input
            type="text"
            name="asked_salary"
            value={formData.asked_salary}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Days:
          <input
            type="text"
            name="days"
            value={formData.days}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Curriculum:
          <input
            type="text"
            name="curriculum"
            value={formData.curriculum}
            onChange={handleChange}
            required
          />
        </label>
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default FindTutors;
