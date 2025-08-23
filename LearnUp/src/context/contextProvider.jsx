import React, { createContext, useEffect, useMemo, useState } from "react";
import axios from "axios";

export const storeContext = createContext({
  api: null,          // shared axios instance
  token: null,        // current token (string or null)
  setToken: () => {}, // setter you can call after /api/login or /api/register
  user: null,         // optional user cache if you want it
  setUser: () => {},
});

export const ContextProvider = ({ children }) => {
  // base URL from Vite env (set to https://learnup.rf.gd on Vercel)
  const baseURL = (import.meta.env.VITE_API_BASE_URL || "http://localhost:8000")
    .replace(/\/+$/, "");

  // minimal app state
  const [token, _setToken] = useState(() => localStorage.getItem("token") || null);
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem("user");
    return saved ? JSON.parse(saved) : null;
  });

  // one shared axios client
  const api = useMemo(() => axios.create({ baseURL }), [baseURL]);

  // keep Authorization header in sync with token
  useEffect(() => {
    if (token) {
      api.defaults.headers.common.Authorization = `Bearer ${token}`;
      localStorage.setItem("token", token);
    } else {
      delete api.defaults.headers.common.Authorization;
      localStorage.removeItem("token");
    }
  }, [token, api]);

  // optional: auto-clear token on 401
  useEffect(() => {
    const id = api.interceptors.response.use(
      (r) => r,
      (err) => {
        if (err.response?.status === 401) {
          _setToken(null);
          setUser(null);
          localStorage.removeItem("user");
        }
        return Promise.reject(err);
      }
    );
    return () => api.interceptors.response.eject(id);
  }, [api]);

  // expose just api + bare state; no auth functions here
  const setToken = (t) => _setToken(t);

  return (
    <storeContext.Provider value={{ api, token, setToken, user, setUser }}>
      {children}
    </storeContext.Provider>
  );
};
