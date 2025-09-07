import { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";
import { storeContext } from "../context/contextProvider";
import axios from "axios";
import './PaymentVoucher.css';

const PaymentVoucher = () => {
  const { tutionId } = useParams();
  const { user, token } = useContext(storeContext);
  const [voucher, setVoucher] = useState(null);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000";

  useEffect(() => {
    if (user?.role === "tutor") {
      fetchVoucher();
    }
  }, [tutionId, user]);

  const fetchVoucher = async () => {
    try {
      const res = await axios.get(`${apiBaseUrl}/api/confirmed-tuition/invoice/${tutionId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      console.log(res.data);
      // Assuming your backend will return the finalized salary
      const amount = res.data.salary * 0.3; 
      setVoucher({ ...res.data, voucherAmount: amount });
    } catch (err) {
      setError(err.response?.data?.error || "Failed to fetch voucher.");
    }
  };

  const markPayment = async () => {
    try {
      const res = await axios.post(`${apiBaseUrl}/api/payment-marked/${tutionId}`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      setSuccess(res.data.message);
    } catch (err) {
      setError(err.response?.data?.error || "Failed to mark payment.");
    }
  };

  if (error) return <p className="error-message">{error}</p>;
  if (!voucher) return <p>Loading voucher...</p>;

  return (
    <div className="voucher-container">
      <h2>Payment Voucher</h2>
      <p><strong>Tuition ID:</strong> {voucher.tutionId}</p>
      <p><strong>Learner ID:</strong> {voucher.learnerId}</p>
      <p><strong>Finalized Salary:</strong> ${voucher.salary}</p>
      <p><strong>Voucher Amount (30%):</strong> ${voucher.voucherAmount}</p>
      <p><strong>Status:</strong> {voucher.status || "Pending"}</p>

      {!voucher.status || voucher.status !== "Completed" ? (
        <button className="mark-payment-btn" onClick={markPayment}>
          Mark Payment Received
        </button>
      ) : (
        <p>Payment Completed ✅</p>
      )}

      {success && <p className="success-message">{success}</p>}
    </div>
  );
};

export default PaymentVoucher;
