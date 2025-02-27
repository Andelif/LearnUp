import React from "react";
import { useNavigate } from "react-router-dom";
import { useState } from "react";
import { storeContext } from "../context/contextProvider";
import { useContext } from "react";
import "./LandingPage.css";

const LandingPage = () => {
  const categories = [
    { id: 1, name: "Mathematics", icon: "📐" },
    { id: 2, name: "Science", icon: "🔬" },
    { id: 3, name: "English", icon: "📖" },
    { id: 4, name: "Programming", icon: "💻" },
    { id: 5, name: "Music", icon: "🎵" },
    { id: 6, name: "Arts", icon: "🎨" },
    { id: 7, name: "BSC", icon: "🔬" },
  ];
  const { user } = useContext(storeContext);
  const navigate=useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  const [tuitionRequests, setTuitionRequests] = useState([]);
  const [filteredResults, setFilteredResults] = useState([]);

  useEffect(() => {
    // Fetch tuition requests from the backend
    const fetchTuitionRequests = async () => {
      try {
        const response = await fetch("http://localhost:4000/api/tuition-requests");
        const data = await response.json();
        setTuitionRequests(data);
      } catch (error) {
        console.error("Error fetching tuition requests:", error);
      }
    };
    fetchTuitionRequests();
  }, []);

  const handleSearch = () => {
    if (!searchQuery) {
      setFilteredResults([]);
      return;
    }
    const results = tuitionRequests.filter(request =>
      request.subject.toLowerCase().includes(searchQuery.toLowerCase()) ||
      request.location.toLowerCase().includes(searchQuery.toLowerCase())
    );
    setFilteredResults(results);
  };

  const handleSignUp = () => {
    navigate('/signup');
}

  return (
    <div className="landing-container">
      {/* Hero Section 
      <section className="hero-section">
        <h1>Find the Best Tutors for Your Learning Needs!</h1>
        <p>Search by Subject, Location, or Tutor Name</p>
        <input type="text" placeholder="Search tutors..." className="search-bar" />
        <div className="hero-buttons">
          <button className="btn-light">Find a Tutor</button>
          
        </div>
      </section>
      */}
      <div className="img-banner">
      <img  className="img-container" src="src/assets/banner.png" alt="" />
      <p className="banner-p">Welcome to LearnUp, {user?.name || "Guest"}!</p> 
      </div>
      
      <section className="hero-section">
        <h1>Find the Best Tuition Opportunities According to Your Skills!</h1>
        <p>Search by Location or Grade</p>
        <input type="text" placeholder="Search tutors..." className="search-bar" value={searchQuery}
         onChange={(e) => setSearchQuery(e.target.value)}/>
        <div className="hero-buttons">
          <button className="btn-light" onClick={handleSearch}>Find Tuition</button>
          
        </div>
      </section>
      {filteredResults.length > 0 && (
        <section className="search-results">
          <h2>Search Results</h2>
          <ul>
            {filteredResults.map((request) => (
              <li key={request.id} className="result-item">
                <strong>{request.subject}</strong> - {request.location} ({request.grade})
              </li>
            ))}
          </ul>
        </section>
      )}

      {/* Features Section */}
      <section className="features-section">
        <h2>Why Choose LearnUp?</h2>
        <div className="features-grid">
          <div className="feature-box">✔ Verified Tutors</div>
          <div className="feature-box">✔ Flexible Scheduling</div>
          <div className="feature-box">✔ Secure & Reliable</div>
          <div className="feature-box">✔ Affordable & Transparent</div>
        </div>
      </section>

      {/* Categories Section */}
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

      {/* How It Works Section */}

      <section className="how-it-works">
  <h2>
    The ways <span className="highlight">Learners</span> can connect with us.
  </h2>
  <div className="steps-container">
    <div className="curve-line"></div> {/* Dashed curved line */}
    <div className="step">
      <div className="step-icon">👤</div>
      <h3>Create Profile</h3>
      <p>Create a profile to get more learning benefits from our website.</p>
    </div>
    <div className="step">
      <div className="step-icon">📝</div>
      <h3>Submit Requirements</h3>
      <p>Fill up expected tutor requirements & submit the request.</p>
    </div>
    <div className="step">
      <div className="step-icon">📄</div>
      <h3>Get Tutors' CV</h3>
      <p>Based on your requirements, we will provide some experienced tutors' CVs.</p>
    </div>
    <div className="step">
      <div className="step-icon">⭐</div>
      <h3>Select Your Tutor</h3>
      <p>Evaluate suggested tutors & start learning with your favorite one.</p>
    </div>
  </div>
</section>


<section className="tutor-steps">
  <h2>
    The ways <span className="highlight">Tutors</span> can connect with us.
  </h2>
  <div className="steps-container">
    <div className="curve-line"></div> 
    <div className="step">
      <div className="step-icon">👤</div>
      <h3>Create Profile</h3>
      <p>Create your profile in minutes with sign up information.</p>
    </div>
    <div className="step">
      <div className="step-icon">✔️</div> 
      <h3>Complete Profile</h3>
      <p>Make your profile at least 80% to get fast responses.</p>
    </div>
    <div className="step">
      <div className="step-icon">🔍</div>  
      <h3>Apply for Tuition Job</h3>
      <p>Visit “Job Board” daily & apply for desired tuition jobs.</p>
    </div>
    <div className="step">
      <div className="step-icon">🎓</div>  
      <h3>Start Tutoring</h3>
      <p>Be confident in the first meet & start tutoring.</p>
    </div>
  </div>
</section>




      {/* Join LearnUp Section */}
      <section className="join-learnup">
        <h2>Join LearnUp Today!</h2>
        <p>Take the next step in your learning journey. Whether you're a student looking for the perfect tutor or an expert wanting to share knowledge, LearnUp is the place for you.</p>
        <div className="join-options">
          <button className="btn-light" onClick={handleSignUp}>Join as a Student</button>
          <button className="btn-dark" onClick={handleSignUp}>Join as a Tutor</button>
        </div>
      </section>
    </div>
  );
};

export default LandingPage;
