import React from "react";
import "./PrivacyPolicy.css";

const PrivacyPolicy = () => {
  return (
    <div className="privacy-policy-container">
      <h1>Privacy Policy</h1>
      <section className="intro">
        <h2>About LearnUp</h2>
        <p>
          Welcome to LearnUp! We, at LearnUp, are committed to safeguarding your privacy while ensuring that your
          online experience is both enjoyable and secure. This privacy policy explains how we collect, use, and protect
          your personal information when you visit our website <a href="/">LearnUp</a>
        </p>
        <p>
          By using LearnUp, you agree to the practices described in this policy. Please note that this policy applies
          only to our website and not to third-party sites that may be linked from our pages.
        </p>
      </section>

      <section className="collected-info">
        <h2>Information We Collect</h2>
        <p>
          You can visit LearnUp without providing any personal details. However, if you choose to register or engage with
          our services, we may collect the following types of personal information:
        </p>
        <ul>
          <li>Your name, email address, contact number, and other contact details.</li>
          <li>Transaction history or other details you share with us through forms.</li>
          <li>Information about your interactions with our site (e.g., IP address, browser type, usage patterns).</li>
          <li>Data collected via cookies for personalized content and user experience enhancement.</li>
        </ul>
      </section>

      <section className="usage-info">
        <h2>How We Use Your Information</h2>
        <p>We may use the information collected for the following purposes:</p>
        <ul>
          <li>To provide and improve the services and features available on LearnUp.</li>
          <li>To assist you in managing your LearnUp account and enhance your learning experience.</li>
          <li>To send you relevant notifications regarding courses, updates, and promotional offers.</li>
          <li>For security and to troubleshoot any issues related to the site.</li>
          <li>To conduct analysis and research on user behavior and preferences.</li>
        </ul>
      </section>

      <section className="childrens-privacy">
        <h2>Children's Privacy</h2>
        <p>
          LearnUp is designed for individuals aged 11 and above. We do not knowingly collect personal or financial
          information from children under the age of 11. If we discover that a child has provided us with personal
          information, we will take appropriate measures to delete such data.
        </p>
      </section>

      <section className="third-party">
        <h2>Third-Party Links</h2>
        <p>
          Our website may contain links to third-party sites. We are not responsible for the privacy practices or content
          of these external sites. Please review their privacy policies before providing any personal information.
        </p>
      </section>
    </div>
  );
};

export default PrivacyPolicy;
