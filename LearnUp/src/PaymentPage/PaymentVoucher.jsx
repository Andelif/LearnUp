import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { toast } from "react-toastify";
import axios from "axios";

const PaymentVoucher = () => {
    const { tutionId } = useParams(); // Get tuition ID from URL
    console.log(tutionId);
    const [voucher, setVoucher] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null); // To track error state
    const token = localStorage.getItem("token"); // Assuming token is stored in localStorage
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || "http://localhost:8000"; // Base URL from environment

    useEffect(() => {
        if (!token) {
            toast.error("Unauthorized access!");
            setLoading(false);
            return;
        }

        const fetchVoucher = async () => {
            try {
                const { data } = await axios.get(
                    `${apiBaseUrl}/api/confirmed-tuition/invoice/${tutionId}`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                setVoucher(data);
            } catch (error) {
                setError("Failed to fetch payment voucher");
                toast.error("Failed to fetch payment voucher");
            } finally {
                setLoading(false);
            }
        };

        fetchVoucher();
    }, [tutionId, token, apiBaseUrl]);

    const markPaid = async () => {
        try {
            await axios.post(
                `${apiBaseUrl}/api/payment-marked/${tutionId}`,
                {},
                { headers: { Authorization: `Bearer ${token}` } }
            );
            toast.success("Payment marked successfully");
        } catch (error) {
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
