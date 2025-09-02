import React from "react";
import "./TermsAndConditions.css"; // make sure the filename matches

const TermsAndConditions = () => {
  return (
    <div className="terms-container">
      <h1>Terms &amp; Conditions</h1>

      <p>
        Please read these terms and conditions carefully before using this site,
        installing, downloading, or utilizing any apps or services from LearnUp.
        The contact and deed are tied to rapport with you for the use of our site,
        and you are unconsciously melting with our terms and conditions as a result.
      </p>

      <p>
        You (“member, customer, reader, teacher, purchaser or seller”) accept to be
        bound by this Agreement by clicking Accept or assent to the Terms of Use
        when this option is made available to you, or by using the Website in whatever
        way, including but not limited to browsing or searching the Web. This agreement
        applies to all Web users, including but not limited to participants in online forums.
      </p>

      <h2>Tutor&apos;s Responsibilities</h2>
      <ul className="terms-list">
        <li>Before applying for any tuition job, check details (location, remuneration, teaching time, etc.). Every tuition job requirement is irreversible.</li>
        <li>You can’t cancel after taking any tuition job reference (parent’s phone number) and must give regular updates to our representative.</li>
        <li>Our representative may contact you during processing; you agree to cooperate.</li>
        <li>After referring a tuition job, you accept full responsibility. If a parent cancels due to your negligence or you cancel for personal reasons, you owe full accountability to LearnUp.</li>
        <li>You must pay the full service charge of the tuition job as compensation. Please cooperate with our representatives. In the first meeting with the parent, bring a relative/friend/well-wisher (especially for female tutors).</li>
        <li>Violating any terms may permanently inactivate your tutor profile, disabling future services.</li>
        <li>LearnUp is a tech-based online service platform. Ensure your own safety; LearnUp does not accept responsibility for this.</li>
      </ul>

      <h2>Service Charge</h2>
      <ul className="terms-list">
        <li>Each tuition job requires 60% payment within five days (one-time).</li>
        <li>For two-month or crash programs, 40% payment within five days of confirmation (one-time).</li>
        <li>For one month jobs, 25% payment within five days of confirmation (one-time).</li>
        <li>If a tuition job is cancelled within the first month due to your lack of time/sincerity/dedication, or you cancel for personal reasons, you are responsible. Refunds of service charges (if applicable) will be credited to your wallet; within 45 days you will receive another tuition job on a priority basis.</li>
      </ul>

      <h2>LearnUp is a Marketplace</h2>
      <ul className="terms-list">
        <li>LearnUp connects those seeking tutoring with those willing to teach. Basic conditions are set by guardians; LearnUp cannot alter them and is not liable for their actions.</li>
        <li>Tutors communicate with guardians via the platform directory. LearnUp has no control over customers’ actions/omissions and makes no guarantees about service quality.</li>
        <li>LearnUp helps users find tutors/tuition. Disputes arising from tuition agreements between tutor and learners are their responsibility.</li>
      </ul>

      <h2>Membership Expediency</h2>
      <p>
        If you are under 18, you may not register as a Member or use the Site without
        a parent/guardian’s permission and supervision. By accessing the Platform and/or
        Services, you represent and warrant that you have the authority to accept these Terms.
        You commit to:
      </p>
      <ul className="terms-list">
        <li>Retain and promptly update Registration Data and any information you provide to keep it accurate, current, and complete.</li>
        <li>Acknowledge any instances of non-compliance.</li>
      </ul>

      <h2>Privacy</h2>
      <p>
        Without your specific consent, we will not sell or rent your personal information to
        third parties for marketing purposes, and we will only use your information as
        described in the Privacy Policy.
      </p>
    </div>
  );
};

export default TermsAndConditions;
