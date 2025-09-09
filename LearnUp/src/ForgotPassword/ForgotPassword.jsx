import React, { useState, useContext } from "react";
import { storeContext } from "../context/contextProvider";

const ForgotPassword = () => {
    const { api } = useContext(storeContext);

    const [step, setStep] = useState(1);        // 1=email, 2=code, 3=new pwd
    const [loading, setLoading] = useState(false);
    const [msg, setMsg] = useState("");
    const [err, setErr] = useState("");

    const [email, setEmail] = useState("");
    const [code, setCode] = useState("");
    const [password, setPassword] = useState("");
    const [confirm, setConfirm] = useState("");

    const handleSendCode = async (e) => {
        e.preventDefault();
        setErr(""); setMsg(""); setLoading(true);

        try {
            const response = await api.post("/api/password/forgot", { email });
            setMsg(response.data.message); // Success message from backend
            setStep(2); // Proceed to OTP verification step
        } catch (err) {
            // Handle errors: either display a generic message or specific error returned by backend
            setErr(err.response?.data?.message || "If the email exists, a verification code has been sent.");
        } finally {
            setLoading(false);
        }
    };


    const handleVerifyCode = async (e) => {
        e.preventDefault();
        setErr(""); setMsg(""); setLoading(true);

        try {
            const response = await api.post("/api/password/verify", { email, code });
            setMsg("Code verified. Please set a new password.");
            setStep(3); // Move to password reset step
        } catch (err) {
            setErr(err.response?.data?.message || "Invalid or expired code.");
        } finally {
            setLoading(false);
        }
    };


    const handleResetPassword = async (e) => {
        e.preventDefault();
        setErr(""); setMsg(""); setLoading(true);

        // Check if password and confirm password match
        if (password !== confirm) {
            setLoading(false);
            setErr("Passwords do not match.");
            return;
        }

        try {
            const response = await api.post("/api/password/reset", { email, code, password });
            setMsg("Password updated successfully. You can now sign in.");
            setStep(1); // Reset form to the email input step
        } catch (err) {
            setErr(err.response?.data?.message || "Reset failed. Try again.");
        } finally {
            setLoading(false);
        }
    };


    return (
        <div className="auth-card">
            <h2>Reset your password</h2>
            {msg && <p className="info">{msg}</p>}
            {err && <p className="error">{err}</p>}

            {step === 1 && (
                <form onSubmit={handleSendCode}>
                    <label>Email</label>
                    <input
                        type="email"
                        className="input-field"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="your@email.com"
                    />
                    <button className="btn" disabled={loading} type="submit">
                        {loading ? "Sending..." : "Send code"}
                    </button>
                </form>
            )}

            {step === 2 && (
                <form onSubmit={handleVerifyCode}>
                    <label>Verification code</label>
                    <input
                        type="text"
                        className="input-field"
                        inputMode="numeric"
                        pattern="[0-9]*"
                        maxLength={6}
                        required
                        value={code}
                        onChange={(e) => setCode(e.target.value.replace(/\D/g, ""))}
                        placeholder="6-digit code"
                    />
                    <button className="btn" disabled={loading} type="submit">
                        {loading ? "Verifying..." : "Verify"}
                    </button>
                </form>
            )}

            {step === 3 && (
                <form onSubmit={handleResetPassword}>
                    <label>New password</label>
                    <input
                        type="password"
                        className="input-field"
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="Enter new password"
                        minLength={8}
                    />
                    <label>Confirm new password</label>
                    <input
                        type="password"
                        className="input-field"
                        required
                        value={confirm}
                        onChange={(e) => setConfirm(e.target.value)}
                        placeholder="Re-enter new password"
                        minLength={8}
                    />
                    <button className="btn" disabled={loading} type="submit">
                        {loading ? "Saving..." : "Reset password"}
                    </button>
                </form>
            )}
        </div>
    );
};

export default ForgotPassword;
