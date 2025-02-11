import React, { createContext, useState, useEffect } from "react";

// ✅ Export the context so it can be used in other components
export const storeContext = createContext({
  user: null,
  token: null,
  setUser: () => {},
  setToken: () => {},
  url: "",
});

export const ContextProvider = ({ children }) => {
  // ✅ Load user and token from localStorage on initial render
  const [user, setUser] = useState(() => {
    const savedUser = localStorage.getItem("user");
    return savedUser ? JSON.parse(savedUser) : null;
  });

  const [token, _setToken] = useState(() => localStorage.getItem("token") || null);

  const url = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

  // ✅ Function to update both state and localStorage
  const setToken = (newToken) => {
    _setToken(newToken);
    if (newToken) {
      localStorage.setItem("token", newToken);
    } else {
      localStorage.removeItem("token");
    }
  };

  // ✅ Load user and token from localStorage on mount (to handle refresh)
  useEffect(() => {
    const savedToken = localStorage.getItem("token");
    const savedUser = localStorage.getItem("user");

    if (savedToken) setToken(savedToken);
    if (savedUser) setUser(JSON.parse(savedUser));
  }, []);

  return (
    <storeContext.Provider value={{ user, token, setUser, setToken, url }}>
      {children}
    </storeContext.Provider>
  );
};
