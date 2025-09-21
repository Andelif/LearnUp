const ProfilePage = () => {
  const { api, user, token } = useContext(storeContext);
  const [formData, setFormData] = useState({});
  const [isEditing, setIsEditing] = useState(false);
  const [isNewProfile, setIsNewProfile] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  const authHeader = {
    headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
  };

  useEffect(() => {
    if (user?.id && user?.role && token) {
      console.log("[FRONTEND] Fetching user data for:", user);
      fetchUserData();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id, user?.role, token]);

  const fetchUserData = async () => {
    console.log("[FRONTEND] fetchUserData called");
    setError("");
    setSuccess("");
    setIsNewProfile(false);
    setIsEditing(false);

    try {
      const { data } = await api.get(`/api/${user.role}s/${user.id}`, authHeader);
      console.log("[FRONTEND] fetchUserData success:", data);
      setFormData(data || {});
    } catch (e) {
      console.error("[FRONTEND] fetchUserData error:", e?.response || e);
      if (e?.response?.status === 404) {
        const base =
          user.role === "tutor"
            ? { ...DEFAULT_TUTOR_FIELDS }
            : { ...DEFAULT_LEARNER_FIELDS, full_name: user?.name || "" };

        setFormData({ user_id: user.id, ...base });
        setIsNewProfile(true);
        setIsEditing(true);
        setError("No profile found. Please create your profile.");
        return;
      }
      setError(
        e?.response?.data?.message || e?.message || "Failed to fetch user data."
      );
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    console.log("[FRONTEND] handleSubmit called with formData:", formData);

    try {
      let editable = pickEditable(user.role, formData);
      editable = normalizeForSubmit(user.role, editable);
      console.log("[FRONTEND] Normalized payload:", editable);

      let payload;
      let headers;

      if (editable.profile_picture instanceof File) {
        payload = new FormData();
        Object.keys(editable).forEach((k) => {
          if (editable[k] !== null && editable[k] !== undefined) {
            payload.append(k, editable[k]);
          }
        });
        headers = {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
          "Content-Type": "multipart/form-data",
        };
        console.log("[FRONTEND] Submitting with FormData payload");
      } else {
        payload = editable;
        headers = {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        };
        console.log("[FRONTEND] Submitting with JSON payload:", payload);
      }

      let res;
      if (isNewProfile) {
        console.log("[FRONTEND] Sending POST request to create profile");
        res = await api.post(`/api/${user.role}s`, payload, { headers });
      } else {
        console.log("[FRONTEND] Sending PUT request to update profile");
        res = await api.put(`/api/${user.role}s/${user.id}`, payload, { headers });
      }

      console.log("[FRONTEND] API response:", res?.data);
      setSuccess(res?.data?.message || "Profile saved!");
      setTimeout(() => setIsEditing(false), 800);
      fetchUserData();
    } catch (err) {
      console.error("[FRONTEND] handleSubmit error:", err?.response || err);
      setError(
        err?.response?.data?.message ||
          err?.response?.data?.error ||
          err?.message ||
          "Error saving profile."
      );
    }
  };

  const handleDelete = async () => {
    console.log("[FRONTEND] handleDelete called for user_id:", user?.id);
    try {
      await api.delete(`/api/${user.role}s/${user.id}`, authHeader);
      console.log("[FRONTEND] Profile deleted successfully");
      setFormData({});
      setIsNewProfile(true);
      setIsEditing(true);
      setSuccess("Profile deleted successfully.");
    } catch (err) {
      console.error("[FRONTEND] handleDelete error:", err?.response || err);
      setError(
        err?.response?.data?.message ||
          err?.response?.data?.error ||
          err?.message ||
          "Error deleting profile."
      );
    }
  };
}
