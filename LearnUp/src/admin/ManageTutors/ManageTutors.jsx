import React, { useContext, useEffect, useState } from "react";
import { storeContext } from "../../context/contextProvider";
import "./ManageTutors.css";

const ManageTutors = () => {
  const { api } = useContext(storeContext);
  const [tutors, setTutors] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const { data } = await api.get("/api/admin/tutors");
        if (alive) setTutors(Array.isArray(data) ? data : []);
      } catch (error) {
        console.error("Error fetching tutors:", error);
      } finally {
        if (alive) setLoading(false);
      }
    })();
    return () => { alive = false; };
  }, [api]);

  const handleDelete = async (tutorId) => {
    if (!window.confirm("Are you sure you want to delete this tutor?")) return;
    try {
      await api.delete(`/api/admin/tutors/${tutorId}`);
      setTutors((prev) => prev.filter((t) => t.TutorID !== tutorId));
    } catch (error) {
      console.error("Error deleting tutor:", error);
    }
  };

  if (loading) {
    return (
      <div className="loader-container">
        <div className="loader"></div>
        <p className="loading-text">Loading...</p>
      </div>
    );
  }

  return (
    <div>
      <h2>Manage Tutors</h2>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Address</th>
            <th>Salary</th>
            <th>Availability</th>
            <th>Qualifications</th>
            <th>Experience</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          {tutors.map((tutor) => (
            <tr key={tutor.TutorID}>
              <td>{tutor.TutorID}</td>
              <td>{tutor.full_name}</td>
              <td>{tutor.address}</td>
              <td>{tutor.preferred_salary}</td>
              <td>{tutor.availability}</td>
              <td>{tutor.qualifications}</td>
              <td>{tutor.experience}</td>
              <td>
                <button onClick={() => handleDelete(tutor.TutorID)}>Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default ManageTutors;
