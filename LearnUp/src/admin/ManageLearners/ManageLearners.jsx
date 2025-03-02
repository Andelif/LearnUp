import React, { useContext, useEffect, useState } from "react";
import axios from "axios";
import { storeContext } from "../../context/contextProvider";

const ManageLearners = () => {
  const [learners, setLearners] = useState([]);
  const {token}=useContext(storeContext);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/admin/learners", {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((response) => {
        setLearners(response.data);
      })
      .catch((error) => {
        console.error("Error fetching learners:", error);
      });
  }, []);
  const handleDelete = (learnerId) => {
    // Confirm deletion
    if (window.confirm("Are you sure you want to delete this learner?")) {
      axios
        .delete(`http://localhost:8000/api/admin/learners/${learnerId}`, {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
          // If successful, remove the learner from the state
          setLearners((prevLearners) =>
            prevLearners.filter((learner) => learner.LearnerID !== learnerId)
          );
        })
        .catch((error) => {
          console.error("Error deleting learner:", error);
        });
    }
  };

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
              <td>
                <button onClick={() => handleDelete(learner.LearnerID)}>Delete</button>
              </td> 
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default ManageLearners;
