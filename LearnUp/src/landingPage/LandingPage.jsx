import React, { useEffect, useState, useContext } from "react";
import { useNavigate } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import "./LandingPage.css";

const LandingPage = () => {
  const categories = [
    { id: 1, name: "Mathematics", icon: "\uD83D\uDCC0" },
    { id: 2, name: "Science",     icon: "\uD83D\uDD2C" },
    { id: 3, name: "English",     icon: "\uD83D\uDCD6" },
    { id: 4, name: "Programming", icon: "\uD83D\uDCBB" },
    { id: 5, name: "Music",       icon: "\uD83C\uDFB5" },
    { id: 6, name: "Arts",        icon: "\uD83C\uDFA8" },
    { id: 7, name: "BSC",         icon: "\uD83D\uDD2C" },
  ];

  const { user, api } = useContext(storeContext);
  const navigate = useNavigate();

  const [searchQuery, setSearchQuery] = useState("");
  const [tuitionRequests, setTuitionRequests] = useState([]);
  const [filteredResults, setFilteredResults] = useState([]);

  useEffect(() => {
    const fetchTuitionRequests = async () => {
      try {
        const { data } = await api.get("/api/tuition-requests/all");
        setTuitionRequests(data);
        // console.log(data);
      } catch (error) {
        console.error("Error fetching tuition requests:", error);
      }
    };
    fetchTuitionRequests();
  }, [api]);

  const handleJobClick = (id) => navigate(`/jobs/${id}`);
  const handleSignUp  = () => navigate("/signup");

  useEffect(() => {
    if (!searchQuery) {
      setFilteredResults([]);
      return;
    }
    const q = searchQuery.toLowerCase();
    const results = tuitionRequests.filter((request) => {
      const subject  = (request.subject || request.subjects || "").toLowerCase();
      const location = (request.location || "").toLowerCase();
      return subject.includes(q) || location.includes(q);
    });
    setFilteredResults(results);
  }, [searchQuery, tuitionRequests]);

  return (
    <div className="landing-container">
      <div className="img-banner">
        <img className="img-container" src="src/assets/banner.png" alt="Banner" />
        <p className="banner-p">Welcome to LearnUp, {user?.name || "Guest"}!</p>
      </div>

      <section className="hero-section">
        <h1>Find the Best Tuition Opportunities According to Your Skills!</h1>
        <p>Search by Location or Grade</p>
        <input
          type="text"
          placeholder="Search tuitions..."
          className="search-bar"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
      </section>

      {filteredResults.length > 0 && (
        <section className="search-results">
          <h2>Search Results</h2>
          <ul>
            {filteredResults.map((request) => (
              <li
                key={request.TutionID}
                className="result-item"
                onClick={() => handleJobClick(request.TutionID)}
                style={{ cursor: "pointer" }}
              >
                <strong>{request.subject || request.subjects}</strong> - {request.location} grade-{request.class}
              </li>
            ))}
          </ul>
        </section>
      )}

      <section className="features-section">
        <h2>Why Choose LearnUp?</h2>
        <div className="features-grid">
          <div className="feature-box">✔ Verified Tutors</div>
          <div className="feature-box">✔ Flexible Scheduling</div>
          <div className="feature-box">✔ Secure & Reliable</div>
          <div className="feature-box">✔ Affordable & Transparent</div>
        </div>
      </section>

      <section className="categories-section">
        <h2 className="section-title">Explore Tuition Categories</h2>
        <div className="categories-grid">
          {categories.map((category) => (
            <div key={category.id} className="category-box">
              <span className="category-icon">{category.icon}</span>
              <p className="category-name">{category.name}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="how-it-works">
        <h2>
          The ways <span className="highlight">Learners</span> can connect with us.
        </h2>
        <div className="steps-container">
          <div className="step"><div className="step-icon">👤</div><h3>Create Profile</h3></div>
          <div className="arrow">&#8594;</div>
          <div className="step"><div className="step-icon">📝</div><h3>Submit Requirements</h3></div>
          <div className="arrow">&#8594;</div>
          <div className="step"><div className="step-icon">📄</div><h3>Get Tutors' CV</h3></div>
          <div className="arrow">&#8594;</div>
          <div className="step"><div className="step-icon">⭐</div><h3>Select Your Tutor</h3></div>
        </div>
      </section>

      <section className="tutor-steps">
        <h2 className="section-title">
          The ways <span className="highlight">Tutors</span> can connect with us.
        </h2>

        <div className="steps-wrapper">
          <div className="step-card">
            <div className="step-content">
              <img src="src/assets/tutor01.png" alt="Create Profile" />
              <h3>Create Your Profile</h3>
              <p>Sign up and provide your details to create your tutor profile.</p>
            </div>
          </div>

          <div className="step-card">
            <div className="step-content">
              <img src="src/assets/tutor02.png" alt="Complete Profile" />
              <h3>Complete Your Profile</h3>
              <p>Fill in necessary details and verify credentials to stand out.</p>
            </div>
          </div>

          <div className="step-card">
            <div className="step-content">
              <img src="src/assets/tutor03.png" alt="Apply for Tuition" />
              <h3>Apply for Tuition Jobs</h3>
              <p>Search for tuition jobs that match your skills and apply.</p>
            </div>
          </div>

          <div className="step-card">
            <div className="step-content">
              <img src="src/assets/tutor04.png" alt="Start Tutoring" />
              <h3>Start Tutoring</h3>
              <p>Get selected by students and start your tutoring journey.</p>
            </div>
          </div>
        </div>
      </section>

      <section className="join-learnup">
        <h2>Join LearnUp Today!</h2>
        <p>
          Take the next step in your learning journey. Whether you're a student looking
          for the perfect tutor or an expert wanting to share knowledge, LearnUp is the
          place for you.
        </p>
        <div className="join-options">
          <button className="btn-light" onClick={() => navigate("/signup")}>
            Join as a Student
          </button>
          <button className="btn-dark" onClick={() => navigate("/signup")}>
            Join as a Tutor
          </button>
        </div>
      </section>
    </div>
  );
};

export default LandingPage;
