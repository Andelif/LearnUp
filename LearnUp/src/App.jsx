import './App.css'
import {BrowserRouter as Router} from "react-router-dom";
import React from 'react'
import AppRoutes from './AppRoutes';

const App = () => {
  return (
    <Router>
      <AppRoutes />
    </Router>
  )
}

export default App