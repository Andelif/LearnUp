import React, { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";
import { toast } from "react-toastify";
import { storeContext } from "../context/contextProvider";

const PaymentVoucher = () => {
  const { tutionId } = useParams(); // (spelling matches your route)
  const { api, token } = useContext(storeContext);

  const [voucher, setVoucher] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!token) {
      toast.error("Unauthorized access!");
      setLoading(false);
      return;
    }

    const fetchVoucher = async () => {
      try {
        const { data } = await api.get(`/api/confirmed-tuition/invoice/${tutionId}`);
        setVoucher(data);
      } catch (e) {
        setError("Failed to fetch payment voucher");
        toast.error("Failed to fetch payment voucher");
      } finally {
        setLoading(false);
      }
    };

    fetchVoucher();
  }, [tutionId, token, api]);

  const markPaid = async () => {
    try {
      await api.post(`/api/payment-marked/${tutionId}`, {});
      toast.success("Payment marked successfully");
    } catch (e) {
      toast.error("Failed to mark payment.");
    }
  };

  if (loading) return <p>Loading...</p>;
  if (error) return <p>{error}</p>;
  if (!voucher) return <p>No voucher found or unauthorized access.</p>;

  return (
    <div>
      <h2>Payment Voucher</h2>
      <p><strong>Voucher ID:</strong> {voucher.voucher_id}</p>
      <p><strong>Amount:</strong> ${voucher.amount}</p>
      <p><strong>Status:</strong> {voucher.status}</p>
      <button onClick={markPaid}>Mark as Paid</button>
    </div>
  );
};

export default PaymentVoucher;
