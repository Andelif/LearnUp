import React from 'react'
import './Footer.css';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="footer">
        <p>&copy; 2025 LearnUp. All Rights Reserved.</p>
        <div className="footer-links">
          <a href="#">About Us</a>
          <Link to="FAQ">FAQ</Link>
          <Link to="/privacy-policy">Privacy Policy</Link>
          <Link to="/TermsAndConditions">Terms and Conditions</Link>
        </div>
      </footer>
  )
}

export default Footer