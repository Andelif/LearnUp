import React, { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";
import { toast } from "react-toastify";
import { storeContext } from "../context/contextProvider";
import "./PaymentVoucher.css";

const PaymentVoucher = () => {
  const { tutionId } = useParams();
  const { api, token } = useContext(storeContext);

  const [voucher, setVoucher] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [processing, setProcessing] = useState(false);

  // Form state for payment
  const [paymentMethod, setPaymentMethod] = useState("Bank_Transfer");
  const [transactionId, setTransactionId] = useState("");
  const [notes, setNotes] = useState("");

  useEffect(() => {
    if (!token) {
      toast.error("Authentication required");
      setLoading(false);
      return;
    }

    const fetchVoucher = async () => {
      try {
        const { data } = await api.get(`/api/payment/voucher/${tutionId}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        console.log(data);
        setVoucher(data.voucher);
      } catch (e) {
        console.error("Error fetching voucher:", e);
        setError(`Failed to fetch payment voucher: ${e.response?.data?.error || e.message}`);
        toast.error("Failed to fetch payment voucher");
      } finally {
        setLoading(false);
      }
    };

    fetchVoucher();
  }, [tutionId, token, api]);

  const markPaid = async () => {
    if (!transactionId && paymentMethod !== "Cash") {
      toast.error("Please enter a transaction ID");
      return;
    }

    setProcessing(true);
    const paymentData = {
      payment_method: paymentMethod,
      transaction_id: transactionId,
      payment_notes: notes,
    };

    try {
      await api.post(`/api/payment/mark-completed/${tutionId}`, paymentData, {
        headers: { Authorization: `Bearer ${token}` }
      });
      toast.success("Payment confirmed successfully");

      // Refresh voucher data
      const { data } = await api.get(`/api/payment/voucher/${tutionId}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      setVoucher(data.voucher);
    } catch (e) {
      console.error("Payment error:", e);
      toast.error("Failed to process payment. Please try again.");
    } finally {
      setProcessing(false);
    }
  };

  const formatDate = (dateString) => {
    if (!dateString) return "Not specified";
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-BD', {
      style: 'currency',
      currency: 'BDT',
      minimumFractionDigits: 0
    }).format(amount);
  };

  if (loading) return (
    <div className="pv-container">
      <div className="pv-loading">
        <div className="pv-loader"></div>
        <h3>Processing Request</h3>
        <p>Retrieving payment information</p>
      </div>
    </div>
  );
  
  if (error) return (
    <div className="pv-container">
      <div className="pv-error">
        <div className="error-icon">
          <i className="fas fa-exclamation-circle"></i>
        </div>
        <h2>Unable to Load Voucher</h2>
        <p className="error-message">{error}</p>
        <button 
          className="retry-btn"
          onClick={() => window.location.reload()}
        >
          <i className="fas fa-redo"></i> Try Again
        </button>
      </div>
    </div>
  );
  
  if (!voucher) return (
    <div className="pv-container">
      <div className="pv-not-found">
        <div className="not-found-icon">
          <i className="fas fa-file-invoice"></i>
        </div>
        <h2>Voucher Not Found</h2>
        <p>No payment voucher found for reference: <strong>{tutionId}</strong></p>
        <div className="not-found-actions">
          <button 
            className="primary-btn"
            onClick={() => window.history.back()}
          >
            <i className="fas fa-arrow-left"></i> Return
          </button>
        </div>
      </div>
    </div>
  );

  return (
    <div className="pv-container">
      <div className="pv-header">
        <h1>Payment Voucher</h1>
        <p className="pv-subtitle">Tuition Payment Management</p>
      </div>

      <div className="pv-content">
        <div className="pv-card">
          <div className="pv-card-header">
            <div className="voucher-info">
              <div className="voucher-id">Voucher: {voucher.voucher_id}</div>
              <div className="voucher-date">Created: {formatDate(voucher.created_at)}</div>
            </div>
            <div className={`status-badge status-${(voucher.status || "pending").toLowerCase()}`}>
              <i className={`fas ${
                voucher.status === 'Completed' ? 'fa-check-circle' : 
                voucher.status === 'Pending' ? 'fa-clock' : 
                voucher.status === 'Failed' ? 'fa-exclamation-triangle' : 'fa-times-circle'
              }`}></i>
              {voucher.status}
            </div>
          </div>

          <div className="payment-breakdown">
            <h3>Payment Breakdown</h3>
            <div className="breakdown-grid">
              <div className="breakdown-row">
                <span className="breakdown-label">Total Tuition Fee :</span>
                <span className="breakdown-value">{(voucher.total_salary)}</span>
              </div>
              <div className="breakdown-row">
                <span className="breakdown-label">LearnUp Media Fee (30%) :</span>
                <span className="breakdown-value">{(voucher.media_fee_amount)}</span>
              </div>
              <div className="breakdown-row">
                <span className="breakdown-label">Tutor Amount (70%) :</span>
                <span className="breakdown-value">{(voucher.tutor_amount)}</span>
              </div>
              <div className="breakdown-row total">
                <span className="breakdown-label">Amount Due :</span>
                <span className="breakdown-value">{(voucher.media_fee_amount)}</span>
              </div>
            </div>
          </div>

          <div className="payment-details">
            <h3>Payment Details</h3>
            <div className="details-grid">
              <div className="detail-row">
                <span className="detail-label"><i className="fas fa-calendar"></i> Due Date :</span>
                <span className="detail-value">{formatDate(voucher.due_date)}</span>
              </div>
              <div className="detail-row">
                <span className="detail-label"><i className="fas fa-info-circle"></i> Status :</span>
                <span className="detail-value">{voucher.status}</span>
              </div>
              {voucher.paid_at && (
                <div className="detail-row">
                  <span className="detail-label"><i className="fas fa-check-circle"></i> Paid Date :</span>
                  <span className="detail-value">{formatDate(voucher.paid_at)}</span>
                </div>
              )}
              {voucher.payment_method && (
                <div className="detail-row">
                  <span className="detail-label"><i className="fas fa-credit-card"></i> Payment Method :</span>
                  <span className="detail-value">{voucher.payment_method.replace('_', ' ')}</span>
                </div>
              )}
            </div>
          </div>

          {voucher.status !== "Completed" && (
            <div className="payment-form-section">
              <h3>Process Payment</h3>
              <p className="form-description">
                Please remit payment of {(voucher.media_fee_amount)} to confirm this tuition arrangement.
              </p>

              <div className="form-row">
                <div className="form-group">
                  <label htmlFor="paymentMethod">Payment Method :</label>
                  <select
                    id="paymentMethod"
                    value={paymentMethod}
                    onChange={(e) => setPaymentMethod(e.target.value)}
                    className="form-control"
                  >
                    <option value="Bank_Transfer">Bank Transfer</option>
                    <option value="Mobile_Banking">Mobile Banking</option>
                    <option value="Cash">Cash</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>

              {paymentMethod !== "Cash" && (
                <div className="form-row">
                  <div className="form-group">
                    <label htmlFor="transactionId">Transaction Reference</label>
                    <input
                      id="transactionId"
                      type="text"
                      value={transactionId}
                      onChange={(e) => setTransactionId(e.target.value)}
                      placeholder="Enter transaction reference number"
                      className="form-control"
                    />
                  </div>
                </div>
              )}

              <div className="form-row">
                <div className="form-group">
                  <label htmlFor="notes">Notes</label>
                  <textarea
                    id="notes"
                    value={notes}
                    onChange={(e) => setNotes(e.target.value)}
                    placeholder="Additional payment information"
                    rows="2"
                    className="form-control"
                  />
                </div>
              </div>

              <button 
                onClick={markPaid} 
                disabled={processing}
                className="submit-payment-btn"
              >
                {processing ? (
                  <>
                    <i className="fas fa-spinner fa-spin"></i>
                    Processing Payment...
                  </>
                ) : (
                  <>
                    <i className="fas fa-lock"></i>
                    Confirm Payment of {(voucher.media_fee_amount)}
                  </>
                )}
              </button>

              <p className="security-note">
                <i className="fas fa-shield-alt"></i> Your payment information is secure and encrypted.
              </p>
            </div>
          )}

          {voucher.status === "Completed" && (
            <div className="payment-completed">
              <div className="completed-icon">
                <i className="fas fa-check-circle"></i>
              </div>
              <h3>Payment Confirmed</h3>
              <p>Your payment of {(voucher.media_fee_amount)} has been successfully processed on {formatDate(voucher.paid_at)}.</p>
              {voucher.transaction_id && (
                <p className="transaction-reference">
                  Reference: <strong>{voucher.transaction_id}</strong>
                </p>
              )}
              <div className="completion-actions">
                <button className="print-btn" onClick={() => window.print()}>
                  <i className="fas fa-print"></i> Print Receipt
                </button>
               
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PaymentVoucher;