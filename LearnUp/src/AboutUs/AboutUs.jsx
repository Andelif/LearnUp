import React from 'react';
import './AboutUs.css'; // Or any CSS file you prefer

const AboutUs = () => {
  return (
    <div className="about-us-container">
      <section className="intro">
        <h1>Welcome to LearnUp</h1>
        <p>
          LearnUp is an innovative online platform designed to simplify the process of connecting students and guardians with qualified tutors. The platform aims to make tuition opportunities both safe and accessible, empowering learners to take charge of their educational journey. With LearnUp, users can explore tutors based on their specific needs, evaluate them through detailed profiles, and book sessions securely and effortlessly.
        </p>
      </section>

      <section className="motivation">
        <h2>Our Motivation</h2>
        <p>
          The driving force behind LearnUp is rooted in addressing the common challenges faced by students and guardians while searching for quality tutors and tuition opportunities. Finding a qualified and reliable tutor can often be a daunting and time-consuming process. Many students and guardians struggle to assess a tutor’s credibility or match their learning needs with available opportunities. LearnUp aims to bridge this gap by creating a streamlined and secure platform that connects learners with the right educators effortlessly.
        </p>
        <p>
          By fostering transparency through detailed tutor profiles, ratings, and reviews, LearnUp empowers users to make informed decisions with confidence. Additionally, LearnUp’s mission is to make education not just accessible but also personalized and inclusive. The platform’s innovative features are designed to cater to diverse learning needs, whether it’s for academic excellence, skill enhancement, or exam preparation.
        </p>
      </section>

      <section className="goals">
        <h2>Our Goals</h2>
        <ul>
          <li>Provide a user-friendly platform for students and guardians to find suitable tutors with ease.</li>
          <li>Ensure safety and reliability by verifying the qualifications and credibility of tutors.</li>
          <li>Create a transparent system where users can make informed decisions through profiles, ratings, and reviews.</li>
          <li>Enable seamless and secure communication between students, guardians, and tutors.</li>
          <li>Offer an efficient booking and scheduling mechanism for tuition sessions.</li>
          <li>Establish a robust payment system that ensures secure and hassle-free transactions.</li>
          <li>Promote personalized education by connecting learners with tutors that meet their unique learning goals.</li>
        </ul>
      </section>

      {/* Footer Section */}
      <section className="learnup-footer">
        <div className="learnup-footer-content">
          <div className="learnup-footer-info">
            <h3>LearnUp</h3>
            <p>LearnUp was founded in 2025. It is Bangladesh's most trusted and leading online platform for guardians, students, and tutors to hire verified tutors or find tuition jobs in various categories from anywhere in the country.</p>
          </div>
          
          <div className="learnup-footer-links">
            
            <ul>
            <h3>Useful Links</h3>
              <li><a href="/">Home</a></li>
              <li><a href="/become-a-tutor">Become a Tutor</a></li>
              <li><a href="/hire-a-tutor">Hire a Tutor</a></li>
              <li><a href="/faq">FAQ</a></li>
            </ul>
          </div>
          
          <div className="learnup-footer-company-info">
            <h4>Company Info</h4>
            <p>Trade licence No: TRAD/DNCC/095492/2025</p>
            <p>E-TIN Number: 023428285525</p>
            <p>BIN Number: 003669024-0102</p>
            <p>Office Address: Level: 4, Rangs Naharz, House: 14, Road: Shahjalal Avenue, Sector 4, Uttara, Dhaka 1230, Bangladesh</p>
          </div>
        </div>
      </section>
    </div>
  );
}

export default AboutUs;
