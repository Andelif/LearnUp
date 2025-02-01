import React from "react";
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
  return (
    <div className="landing-container">
      {/* Header */}
      
      
      {/* Hero Section */}
      <section className="hero-section">
        <h2>Find the Best Tutors for Your Learning Needs!</h2>
        <p>Search by Subject, Location, or Tutor Name</p>
        <input type="text" placeholder="Search tutors..." className="search-bar" />
        <div className="hero-buttons">
          <button className="btn-light">Find a Tutor</button>
          <button className="btn-dark">Become a Tutor</button>
        </div>
      </section>
      
      {/* Why Choose LearnUp */}
      <section className="features-section">
        <h3>Why Choose LearnUp?</h3>
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
      {/* How It Works */}
      <section className="how-it-works">
        <h3>How It Works?</h3>
        <div className="steps-grid">
          <div className="step-box">1. Search for a tutor</div>
          <div className="step-box">2. Check tutor profiles & reviews</div>
          <div className="step-box">3. Book a session & start learning</div>
        </div>
      </section>
      
      {/* Call to Action */}
      <section className="cta-section">
        <h3>Join LearnUp Today!</h3>
        <button className="cta-button">Sign Up</button>
      </section>
      
      
    </div>
  );
};

export default LandingPage;
