import React, { useState } from "react";
import "./FAQ.css";

const FAQ = () => {
  const [openIndex, setOpenIndex] = useState(null);

  const toggleFAQ = (index) => {
    setOpenIndex(openIndex === index ? null : index);
  };

  const tutorFAQs = [
    {
      question: "How can I create a tutor account?",
      answer: "Click at theSign Up as Tutor & fill up all the information you have requested. To make your profile complete, fill up all the blanks. Tutors can check their current profile condition from the Profile  option, it will help to understand how is his/her profile looks like."
    },
    {
      question: "What are the minimum requirements for becoming a tutor?",
      answer: "If you have any skill to share with others then you can create a tutor account on LearnUp. Usually, guardian/student can post their requirements in 13 categories & these are English Medium, Bangla Medium, English Version, Arts, Religious Studies, Test Preparation, Admission Test, Professional Skill development, Special Skill development, Language Learning, Madrasha Medium, Uni Help and Special Child Education. If you have expertise in those categories then you can create an account but the selection will depend on the guardian/student."
    },
    {
      question: "How do I get paid?",
      answer: "In case of physical tutoring (Home tutoring, Group tutoring & Package tutoring),LearnUp will not be involved in any transactions between tutors and guardians/students. We suggest fixing a convenient way and time for both (tutor & guardian/student) to collect your salary"
    },
    {
      question: "Can I edit my profile after registration?",
      answer: "Definitely, you can edit your profile any time but if you are our verified tutor then you have to send us an unlock request to edit your profile & our system will keep your data for the future security purpose."
    }
  ];

  const learnerFAQs = [
    {
      question: "How to post a tutor request?",
      answer: "You (guardian/student) can post your tutor requirements in a very easy method. Click the Hire a Tutor  button on our home page and input your contact number there. After that, fill up all your tutor requirements and then click on the “Submit” button."
    },
    {
      question: "How can I select the best tutor?",
      answer: "You can choose the best one based on their overview, tutoring method, educational background and experiences. Select the best one & call the tutor for two trial classes. If his/her expertise satisfies you & student then confirm the tutor. Kindly maintain the proper respect & professional behaviour during his/her service period."
    },
    {
      question: "Do you verify tutor profiles?",
      answer: "Yes, we verify a tutor’s profile for data authenticity but if guardians/students want, they can collect the photocopy of the last or current educational qualification document from the tutor. As long as the tutor teaches, the document will be kept by the guardian/student.Yes, we verify a tutor’s profile for data authenticity but if guardians/students want, they can collect the photocopy of the last or current educational qualification document from the tutor. As long as the tutor teaches, the document will be kept by the guardian/student."
    },
    {
      question: "Do I have to pay any platform charge?",
      answer: "There are no charges for guardians/students to hire a tutor by using our platform. But there are tuition fees which guardians/students have to pay the tutors and need to confirm the salary based on your needs which we will post on our job board."
    }
  ];

  return (
    <div className="faq-container">
      <h2 className="faq-title">Frequently Asked Questions</h2>
      <div className="faq-sections">
        {/* Tutor Section */}
        <div className="faq-section">
          <h3 className="faq-heading">For Tutors</h3>
          {tutorFAQs.map((faq, index) => (
            <div key={index} className="faq-item">
              <div className="faq-question" onClick={() => toggleFAQ(index)}>
                {faq.question}
                <span className="faq-icon">{openIndex === index ? "−" : "+"}</span>
              </div>
              {openIndex === index && <div className="faq-answer">{faq.answer}</div>}
            </div>
          ))}
        </div>

        {/* Learner Section */}
        <div className="faq-section">
          <h3 className="faq-heading">For Learners</h3>
          {learnerFAQs.map((faq, index) => (
            <div key={index + tutorFAQs.length} className="faq-item">
              <div className="faq-question" onClick={() => toggleFAQ(index + tutorFAQs.length)}>
                {faq.question}
                <span className="faq-icon">{openIndex === index + tutorFAQs.length ? "−" : "+"}</span>
              </div>
              {openIndex === index + tutorFAQs.length && <div className="faq-answer">{faq.answer}</div>}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default FAQ;
