import React, { useContext, useEffect, useState } from "react";
import { storeContext } from "../../context/contextProvider";
import "./ManageLearners.css";

const ManageLearners = () => {
  const { api } = useContext(storeContext);
  const [learners, setLearners] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    let alive = true;
    (async () => {
      try {
        const { data } = await api.get("/api/admin/learners");
        if (alive) setLearners(Array.isArray(data) ? data : []);
      } catch (error) {
        console.error("Error fetching learners:", error);
      } finally {
        if (alive) setLoading(false);
      }
    })();
    return () => { alive = false; };
  }, [api]);

  const handleDelete = async (learnerId) => {
    if (!window.confirm("Are you sure you want to delete this learner?")) return;
    try {
      await api.delete(`/api/admin/learners/${learnerId}`);
      setLearners((prev) => prev.filter((l) => l.LearnerID !== learnerId));
    } catch (error) {
      console.error("Error deleting learner:", error);
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
      <h2>Manage Learners</h2>
      <div className="table-scroll">
        <table border="1">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Guardian Name</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th></th>
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
    </div>
  );
};

export default ManageLearners;
