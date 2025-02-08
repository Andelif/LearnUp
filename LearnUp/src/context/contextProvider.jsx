import React, { createContext, useState } from "react";

// ✅ Export the context so it can be used in other components
export const storeContext = createContext({
  user: null,
  token: null,
  setUser: () => {},
  setToken: () => {},
  url:''
});

export const ContextProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    const savedUser = localStorage.getItem("user");
    return savedUser ? JSON.parse(savedUser) : null;
  });
const url="http://localhost:8000";
  const [token, _setToken] = useState(localStorage.getItem("Access_token") || null);

  // ✅ Make sure setToken updates both state & localStorage
  const setToken = (newToken) => {
    _setToken(newToken);
    if (newToken) {
      localStorage.setItem("Access_token", newToken);
    } else {
      localStorage.removeItem("Access_token");
    }
  };

  return (
    <storeContext.Provider value={{ user, token, setUser, setToken ,url}}>
      {children}
    </storeContext.Provider>
  );
};
