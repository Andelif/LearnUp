import React from "react";
import { useNavigate } from "react-router-dom";
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
      <p className="banner-p">Welcome to LearnUp,{user?.name || "Guest"}!</p> 
      </div>
      
      <section className="hero-section">
        <h1>Find the Best Tutors for Your Learning Needs!</h1>
        <p>Search by Subject, Location, or Tutor Name</p>
        <input type="text" placeholder="Search tutors..." className="search-bar" />
        <div className="hero-buttons">
          <button className="btn-light">Find a Tutor</button>
          
        </div>
      </section>

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
        <h2>How It Works?</h2>
        <div className="steps-grid">
          <div className="step-box">1. Search for a tutor</div>
          <div className="step-box">2. Check tutor profiles & reviews</div>
          <div className="step-box">3. Book a session & start learning</div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="testimonials-section">
        <h2>What Our Students Say</h2>
        <div className="testimonials-grid">
          <div className="testimonial">
            <p>"LearnUp helped me find the perfect tutor. My grades have improved tremendously!"</p>
            <span>- Sarah M.</span>
          </div>
          <div className="testimonial">
            <p>"A fantastic platform with verified tutors. Highly recommended!"</p>
            <span>- James T.</span>
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
