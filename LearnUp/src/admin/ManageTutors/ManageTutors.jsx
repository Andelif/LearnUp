import React, { useEffect, useState } from "react";
import axios from "axios";

const ManageTutors = () => {
  const [tutors, setTutors] = useState([]);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/admin/tutors", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      })
      .then((response) => {
        setTutors(response.data);
      })
      .catch((error) => {
        console.error("Error fetching tutors:", error);
      });
  }, []);

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
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default ManageTutors;
