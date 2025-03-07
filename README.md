# LearnUp

Connect Tutors and Guardians Seamlessly with LearnUp.

## Project Overview

**LearnUp** is a platform where guardians can search for suitable tutors for their children, and tutors can find tuition opportunities that match their preferences. With intuitive features and a clean interface, our platform streamlines the process of connecting tutors and guardians, ensuring a better learning experience for students.

## Title

### LearnUp

## Short Description

LearnUp is a tutor-searching and tuition-finding platform that allows:

- **Guardians** to find the most suitable tutors for their children based on subject, grade, and location.

* **Tutors** to discover tuitions that match their preferences, availability, and expertise.

The platform offers a clean and user-friendly experience, empowering users to easily connect with one another for educational needs.

## Key Features

### For Guardians:

- **Easy Registration**: Create an account to start searching for tutors.
- **Tutor Profiles**: View detailed tutor profiles, including expertise, experience, and reviews.
- **Search & Filter**: Find tutors based on subject, grade, and location.

### For Tutors:

- **Easy Signup**: Register to showcase your expertise and find tuition opportunities.
- **Tuition Listings**: Explore available tuitions with details like subject, schedule, and location.

### General:

- **Responsive Design**: Accessible from desktops, tablets, and smartphones.
- **Secure Login**: Ensures user privacy and secure data handling.

---

## Target Audience

LearnUp is designed for:

- **Guardians**: Parents or guardians seeking qualified tutors for their children.
- **Tutors**: Individuals or professionals looking for tutoring opportunities that align with their skills.
- **Students**: Those looking to improve academic performance with personalized support.

---

## [Figma Link](https://www.figma.com/design/Fzj0GcPvfdRMbjPLvjbcrd/LearnUp?m=auto&t=xquyMiVgqsoUJdpw-1)

---

Tech Stack:

     Backend: Laravel
     Frontend: pHp(using blade) & Bootstrap (css)
     Database: My SQL

Rendering Method:

    Server-Side Rendering (SSR)

UI Design:

     Here is the demo ui design for our app. Our is intended to look like this.
     ## [Figma Link](https://www.figma.com/design/Fzj0GcPvfdRMbjPLvjbcrd/LearnUp?m=auto&t=xquyMiVgqsoUJdpw-1)

Project Features:

     USERS:

          • Multiuser Authentication
          •	CRUD operations
          •	Search Functionality
          •	Apply for Tuition Requests
          •	Messaging System
          •	Payment Integration
          •	Dashboard

     ADMIN:
          •	Admin Authentication
          •	Manage Users
          •	CRUD Operations on Tuition Requests
          •	Search Functionality
          •	Search & Filter
          • View Transactions


## API Endpoints

### Authentication Routes

| **Endpoint**           | **HTTP Method** | **Description**                                                 |
| ---------------------- | --------------- | --------------------------------------------------------------- |
| `/register`            | `POST`          | Register a new user (Tutor/Guardian).                           |
| `/login`               | `POST`          | Login for an existing user.                                     |
| `/logout`              | `POST`          | Logout the current user.                                        |
| `/user`                | `GET`           | Get the authenticated user's details (requires authentication). |
| `/user/update-profile` | `PUT`           | Update the user's profile (requires authentication).            |

### Tuition Request Routes

| **Endpoint**               | **HTTP Method** | **Description**                                                   |
| -------------------------- | --------------- | ----------------------------------------------------------------- |
| `/tuition-requests/all`    | `GET`           | Get all tuition requests.                                         |
| `/tuition-requests/{id}`   | `GET`           | Get a specific tuition request by ID.                             |
| `/tuition-requests/filter` | `GET`           | Filter tuition requests based on criteria (e.g., subject, grade). |
| `/tuition-requests`        | `GET`           | Get all tuition requests for the authenticated user.              |
| `/tuition-requests`        | `POST`          | Create a new tuition request (requires authentication).           |
| `/tuition-requests/{id}`   | `PUT`           | Update an existing tuition request (requires authentication).     |
| `/tuition-requests/{id}`   | `DELETE`        | Delete a tuition request (requires authentication).               |

### Tutor, Learner, and Admin Routes

| **Endpoint**              | **HTTP Method** | **Description**                                               |
| ------------------------- | --------------- | ------------------------------------------------------------- |
| `/tutors`                 | `GET`           | Get all tutors (requires authentication).                     |
| `/learners`               | `GET`           | Get all learners (requires authentication).                   |
| `/admins`                 | `GET`           | Get all admin users (requires authentication).                |
| `/tutors/{id}`            | `PUT`           | Update a tutor's details (requires authentication).           |
| `/learners/{id}`          | `PUT`           | Update a learner's details (requires authentication).         |
| `/admin/learners`         | `GET`           | Get a list of learners for admin (requires authentication).   |
| `/admin/tutors`           | `GET`           | Get a list of tutors for admin (requires authentication).     |
| `/admin/tuition-requests` | `GET`           | Get all tuition requests for admin (requires authentication). |
| `/admin/match-tutor`      | `POST`          | Admin matches a tutor with a tuition request.                 |
| `/admin/learners/{id}`    | `DELETE`        | Delete a learner by admin (requires authentication).          |
| `/admin/tutors/{id}`      | `DELETE`        | Delete a tutor by admin (requires authentication).            |

### Application Routes

| **Endpoint**                       | **HTTP Method** | **Description**                                                        |
| ---------------------------------- | --------------- | ---------------------------------------------------------------------- |
| `/applications`                    | `POST`          | Submit an application for a tuition request (requires authentication). |
| `/applications/check/{tuition_id}` | `GET`           | Check if a tutor has applied for a specific tuition request.           |
| `/tutor/{userId}/stats`            | `GET`           | Get tutor statistics (e.g., number of applications).                   |
| `/learner/{userId}/stats`          | `GET`           | Get learner statistics (e.g., number of tuition requests).             |

### Notification Routes

| **Endpoint**               | **HTTP Method** | **Description**                                       |
| -------------------------- | --------------- | ----------------------------------------------------- |
| `/notifications`           | `GET`           | Get all notifications for the authenticated user.     |
| `/notifications`           | `POST`          | Create a new notification for the authenticated user. |
| `/notifications/{id}/read` | `PUT`           | Mark a notification as read.                          |
| `/notifications/{id}`      | `DELETE`        | Delete a notification by ID.                          |

### Message Routes

| **Endpoint**                | **HTTP Method** | **Description**                                      |
| --------------------------- | --------------- | ---------------------------------------------------- |
| `/messages`                 | `POST`          | Send a message to another user.                      |
| `/messages/{userId}`        | `GET`           | Get messages for a specific user.                    |
| `/messages/seen/{senderId}` | `PUT`           | Mark a message as seen.                              |
| `/matched-users`            | `GET`           | Get a list of matched users for messaging.           |
| `/reject-tutor`             | `POST`          | Reject a tutor's application for a specific tuition. |

### Admin Functionalities

| **Endpoint**              | **HTTP Method** | **Description**                                                   |
| ------------------------- | --------------- | ----------------------------------------------------------------- |
| `/admin/match-tutor`      | `POST`          | Match a tutor with a tuition request (requires admin).            |
| `/admin/tuition-requests` | `GET`           | Get all tuition requests for the admin (requires authentication). |

### Confirmed Tuition Routes

| **Endpoint**               | **HTTP Method** | **Description**                 |
| -------------------------- | --------------- | ------------------------------- |
| `/confirmed-tuitions`      | `GET`           | Get all confirmed tuitions.     |
| `/confirmed-tuitions`      | `POST`          | Create a new confirmed tuition. |
| `/confirmed-tuitions/{id}` | `PUT`           | Update a confirmed tuition.     |
| `/confirmed-tuitions/{id}` | `DELETE`        | Delete a confirmed tuition.     |

---

## Checkpoints

### Checkpoint 1

- Implement the mock UI for landing pages and dashboard from Figma.
- Implement the frontend interface for the landing page.
- Implement the frontend interface for tutor and guardian signup pages.

### Checkpoint 2

- Implement user authentication (registration & login) for both frontend and backend.
- Implement the tutor search and tuition listing pages with appropiate filters.
- Implement the frontend interface for admin

### Checkpoint 3

- Build the backend for search functionality and user profiles with appropiate filters.
- Add admin functionalities for matching the best-match tutors with their students
- Finalize UI/UX design for responsiveness.
- Merge the frontend and backend, ensuring seamless functionality.

---

Setup Instruction:

     Follow the steps below to set up the project on your local system:

     1.	Prerequisites
               Before proceeding, ensure you have the following installed:
               •	PHP (>=8.0)
               •	Composer (PHP Dependency Manager)
               •	Laravel (Latest version)
               •	MySQL (or any preferred database)
               •	Node.js & npm (for frontend dependencies)
               •	Git (optional, but recommended)

     2.	Clone the Repository
               https://github.com/Andelif/LearnUp.git
               cd LearnUp

     3.	Install dependencies
               composer install

               Copy the .env.example file and rename it to .env

               Open the .env file and update the following:
               APP_NAME= Laravel
               APP_ENV=local
               APP_KEY=
               APP_DEBUG=true
               APP_URL=http://localhost
               DB_CONNECTION=mysql
               DB_HOST=127.0.0.1
               DB_PORT=3306
               DB_DATABASE=learnup_db
               DB_USERNAME=root
               DB_PASSWORD=

     4.	Install Frontend Dependencies

               npm install
               npm run build
               npm run dev
               Lastly run “php artisan serve” to run the project

## Team Members

|      Name       | Roll Number |              Email               |   Role   |
| :-------------: | :---------: | :------------------------------: | :------: |
|  Hridita Alam   | 20220104056 |      hriditaalam1@gmail.com      |   Lead   |
|  SK Ali Ahnaf   | 20220104055 |  ahnaf.cse.20220104055@aust.edu  | Frontend |
| Andelif Hossain | 20220104052 | andelif.cse.20220104052@aust.edu | Backend  |
