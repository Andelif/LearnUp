import React, { useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
import "./FindTutors.css";

const FindTutors = () => {
  const { api, token } = useContext(storeContext);
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    class: "",
    subjects: "",
    asked_salary: "",
    location: "",
    days: "",          // STRING per validator
    curriculum: "",
  });

  const handleChange = (e) =>
    setFormData({ ...formData, [e.target.name]: e.target.value });

  const handleSubmit = async (e) => {
    e.preventDefault();

    // required field check (mirror validator)
    const required = ["class", "subjects", "asked_salary", "curriculum", "days", "location"];
    for (const k of required) {
      if (!String(formData[k]).trim()) {
        toast.error(`Please fill the ${k.replace("_", " ")} field.`);
        return;
      }
    }

    if (!token) {
      toast.error("Unauthorized! Please log in first.");
      navigate("/signIn");
      return;
    }

    const payload = {
      class: String(formData.class).trim(),
      subjects: String(formData.subjects).trim(),
      asked_salary: Number(formData.asked_salary) || 0,   // numeric
      curriculum: String(formData.curriculum).trim(),
      days: String(formData.days).trim(),                 // string
      location: String(formData.location).trim(),
      // NOTE: controller uses auth user; no learner_id required
    };

    try {
      const res = await api.post("/api/tuition-requests", payload, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
      });

      if (res.status === 201 || res.status === 200) {
        toast.success(res?.data?.message || "Tuition requirement submitted!");
        setFormData({
          class: "",
          subjects: "",
          asked_salary: "",
          location: "",
          days: "",
          curriculum: "",
        });
      } else {
        toast.error(res.data?.message || "Something went wrong!");
      }
    } catch (err) {
      const errors = err?.response?.data?.errors;
      if (errors && typeof errors === "object") {
        const firstKey = Object.keys(errors)[0];
        const firstMsg = errors[firstKey]?.[0];
        toast.error(firstMsg || "Validation failed.");
      } else {
        const msg =
          err?.response?.data?.message ||
          err?.response?.data?.error ||
          err?.message ||
          "Failed to submit.";
        toast.error(msg);
      }
      console.error("Tuition create failed:", err?.response?.data || err);
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
            type="number"
            name="asked_salary"
            value={formData.asked_salary}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Days (e.g. "3" or "Sun-Tue"):
          <input
            type="text"          // STRING for validator
            name="days"
            value={formData.days}
            onChange={handleChange}
            required
          />
        </label>
        <label>
          Curriculum:
          <select
            name="curriculum"
            value={formData.curriculum}
            onChange={handleChange}
            required
          >
          <option value="">-- Select Curriculum --</option>
          <option value="EV">EV</option>
          <option value="BV">BV</option>
          <option value="EM">EM</option>
         </select>
        </label>
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default FindTutors;
