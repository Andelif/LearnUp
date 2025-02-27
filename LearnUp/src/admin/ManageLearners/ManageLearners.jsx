import React, { useEffect, useState } from "react";
import axios from "axios";

const ManageLearners = () => {
  const [learners, setLearners] = useState([]);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/admin/learners", {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      })
      .then((response) => {
        setLearners(response.data);
      })
      .catch((error) => {
        console.error("Error fetching learners:", error);
      });
  }, []);

  return (
    <div>
      <h2>Manage Learners</h2>
      <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Guardian Name</th>
            <th>Contact Number</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          {learners.map((learner) => (
            <tr key={learner.LearnerID}>
              <td>{learner.LearnerID}</td>
              <td>{learner.full_name}</td>
              <td>{learner.guardian_full_name}</td>
              <td>{learner.contact_number}</td>
              <td>{learner.address}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default ManageLearners;
