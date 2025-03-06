import React, { useContext, useEffect, useState } from "react";
import axios from "axios";
import { storeContext } from "../../context/contextProvider";
import "./ManageTutors.css";

const ManageTutors = () => {
  const [tutors, setTutors] = useState([]);
  const [loading, setLoading] = useState(true); // Track loading state
  const { token } = useContext(storeContext);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/admin/tutors", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        setTutors(response.data);
        setLoading(false); // Set loading to false once data is fetched
      })
      .catch((error) => {
        console.error("Error fetching tutors:", error);
        setLoading(false); // Set loading to false even if there is an error
      });
  }, [token]);

  const handleDelete = (tutorId) => {
    // Confirm deletion
    if (window.confirm("Are you sure you want to delete this tutor?")) {
      axios
        .delete(`http://localhost:8000/api/admin/tutors/${tutorId}`, {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
          // If successful, remove the tutor from the state
          setTutors((prevTutors) =>
            prevTutors.filter((tutor) => tutor.TutorID !== tutorId)
          );
        })
        .catch((error) => {
          console.error("Error deleting tutor:", error);
        });
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
