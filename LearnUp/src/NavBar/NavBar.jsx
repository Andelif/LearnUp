import React from 'react'
import './NavBar.css';
import { Link } from 'react-router-dom';

const NavBar = () => {
  return (
    <header className="header">
        <h1 className="logo">LearnUp</h1>
        <nav className="nav-links">
        <Link to="/">Home</Link>
        <Link >Find Tutors</Link>
        <Link>How It Works</Link>
        <Link >About Us</Link>
        <Link>Contact</Link>
        </nav>
        <Link to="/signIn">
        <button className="sign-in-btn">Sign In</button>
        </Link>
      </header>
  )
}

export default NavBar