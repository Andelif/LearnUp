import React from 'react'
import './Footer.css';
import { Link } from 'react-router-dom';

const Footer = () => {
  return (
    <footer className="footer">
        <p>&copy; 2024 LearnUp. All Rights Reserved.</p>
        <div className="footer-links">
          <a href="#">About Us</a>
          <a href="#">FAQ</a>
          <Link to="/privacy-policy">Privacy Policy</Link>
          <a href="#">Terms of Service</a>
        </div>
      </footer>
  )
}

export default Footer