-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 08:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learnup_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `permission_req` tinyint(1) NOT NULL DEFAULT 0,
  `match_made` int(11) NOT NULL DEFAULT 0,
  `task_assigned` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `user_id`, `full_name`, `address`, `contact_number`, `permission_req`, `match_made`, `task_assigned`, `created_at`, `updated_at`) VALUES
(1, 3, 'Test5', NULL, NULL, 0, 0, NULL, '2025-02-07 13:06:45', '2025-02-07 13:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `ApplicationID` bigint(20) UNSIGNED NOT NULL,
  `tution_id` bigint(20) UNSIGNED NOT NULL,
  `learner_id` bigint(20) UNSIGNED NOT NULL,
  `tutor_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`ApplicationID`, `tution_id`, `learner_id`, `tutor_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, '2025-02-05 11:34:54', '2025-02-05 11:34:54'),
(2, 1, 1, 1, '2025-02-05 11:35:24', '2025-02-05 11:35:24');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learners`
--

CREATE TABLE `learners` (
  `LearnerID` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `guardian_full_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `guardian_contact_number` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `learners`
--

INSERT INTO `learners` (`LearnerID`, `user_id`, `full_name`, `guardian_full_name`, `contact_number`, `guardian_contact_number`, `gender`, `address`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Doe', NULL, NULL, NULL, NULL, NULL, '2025-02-05 11:01:01', '2025-02-05 11:01:01'),
(2, 1, 'John Doe', 'Jane Doe', '01700000000', '01800000000', 'Male', 'Dhaka, Bangladesh', '2025-02-05 11:02:00', '2025-02-05 11:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(10, 'add_role_to_users_table', 2),
(50, '2014_10_12_000000_create_users_table', 3),
(51, '2014_10_12_100000_create_password_resets_table', 3),
(52, '2019_08_19_000000_create_failed_jobs_table', 3),
(53, '2019_12_14_000001_create_personal_access_tokens_table', 3),
(54, '2025_02_04_140132_create_learners_table', 3),
(55, '2025_02_04_140253_create_tutors_table', 3),
(56, '2025_02_04_140317_create_admins_table', 3),
(57, '2025_02_04_140327_create_tuition_requests_table', 3),
(58, '2025_02_04_140341_create_applications_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '0f758cf5fa8a485c446b71cb86f5894efcaf3f388602e15c83f123f2909bdf44', '[\"*\"]', NULL, NULL, '2025-02-05 11:01:01', '2025-02-05 11:01:01'),
(2, 'App\\Models\\User', 1, 'auth_token', 'f24d7d091608e5c6c56ca0900c5eac9dfa99d8d39fd2b36ee154123e54f24d6a', '[\"*\"]', '2025-02-05 11:06:26', NULL, '2025-02-05 11:01:12', '2025-02-05 11:06:26'),
(3, 'App\\Models\\User', 2, 'auth_token', 'c161b1f015272627e3efb4051cec9134f15b06d469ae77c9309af80cfdd1bff3', '[\"*\"]', NULL, NULL, '2025-02-05 11:09:12', '2025-02-05 11:09:12'),
(4, 'App\\Models\\User', 2, 'auth_token', '5ab4e5e2a103ae66c19f5e3c3e159490662001b84dd7079d4f8abcbeb81c77d0', '[\"*\"]', '2025-02-05 11:35:24', NULL, '2025-02-05 11:09:28', '2025-02-05 11:35:24'),
(5, 'App\\Models\\User', 3, 'auth_token', '8ee69bad1c3307f54ab44112b28cd1a45e3b3c79dcfbfe256bd8a82b76e2bb62', '[\"*\"]', NULL, NULL, '2025-02-07 13:06:46', '2025-02-07 13:06:46');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_requests`
--

CREATE TABLE `tuition_requests` (
  `TutionID` bigint(20) UNSIGNED NOT NULL,
  `learner_id` bigint(20) UNSIGNED NOT NULL,
  `class` varchar(255) NOT NULL,
  `subjects` text NOT NULL,
  `asked_salary` int(11) NOT NULL,
  `curriculum` varchar(255) NOT NULL,
  `days` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuition_requests`
--

INSERT INTO `tuition_requests` (`TutionID`, `learner_id`, `class`, `subjects`, `asked_salary`, `curriculum`, `days`, `created_at`, `updated_at`) VALUES
(1, 1, 'Class 10', 'Math, Physics', 4000, 'National', 'Saturday, Monday, Wednesday', '2025-02-05 11:02:21', '2025-02-05 11:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `TutorID` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `preferred_salary` int(11) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `currently_studying_in` varchar(255) DEFAULT NULL,
  `preferred_location` varchar(255) DEFAULT NULL,
  `preferred_time` varchar(255) DEFAULT NULL,
  `availability` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`TutorID`, `user_id`, `full_name`, `address`, `contact_number`, `gender`, `preferred_salary`, `qualification`, `experience`, `currently_studying_in`, `preferred_location`, `preferred_time`, `availability`, `created_at`, `updated_at`) VALUES
(1, 2, 'andelif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-02-05 11:09:12', '2025-02-05 11:09:12'),
(2, 2, 'Alice Smith', 'Chittagong, Bangladesh', '01500000000', 'Female', 5000, 'B.Sc in Mathematics', '3 years', 'Dhaka University', 'Dhaka', 'Evening', 1, '2025-02-05 11:10:24', '2025-02-05 11:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('learner','tutor','admin') NOT NULL DEFAULT 'learner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'John Doe', 'johndoe@example.com', NULL, '$2y$10$ibFleQrBD0i3qU7UYVHO.ugIP0rHyf6JbvYaUFe2Urv7KZPZMMyKy', NULL, '2025-02-05 11:01:01', '2025-02-05 11:01:01', 'learner'),
(2, 'andelif', 'andelif@gmail.com', NULL, '$2y$10$mpBGIW6JVj5BVa4PmiNwMeQZT1SubGp35peYp5WExirVbdXKOYKMu', NULL, '2025-02-05 11:09:12', '2025-02-05 11:09:12', 'tutor'),
(3, 'Test5', 'test5@gmail.com', NULL, '$2y$10$4GfkDTIIQ5U8gYZQr9CE2ObCevTV978TIUMoJZWjPTNWMC/58bMOu', NULL, '2025-02-07 13:06:45', '2025-02-07 13:06:45', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `admins_user_id_unique` (`user_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`ApplicationID`),
  ADD KEY `applications_tution_id_foreign` (`tution_id`),
  ADD KEY `applications_learner_id_foreign` (`learner_id`),
  ADD KEY `applications_tutor_id_foreign` (`tutor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `learners`
--
ALTER TABLE `learners`
  ADD PRIMARY KEY (`LearnerID`),
  ADD KEY `learners_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `tuition_requests`
--
ALTER TABLE `tuition_requests`
  ADD PRIMARY KEY (`TutionID`),
  ADD KEY `tuition_requests_learner_id_foreign` (`learner_id`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`TutorID`),
  ADD KEY `tutors_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `AdminID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `ApplicationID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learners`
--
ALTER TABLE `learners`
  MODIFY `LearnerID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tuition_requests`
--
ALTER TABLE `tuition_requests`
  MODIFY `TutionID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `TutorID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_learner_id_foreign` FOREIGN KEY (`learner_id`) REFERENCES `learners` (`LearnerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_tution_id_foreign` FOREIGN KEY (`tution_id`) REFERENCES `tuition_requests` (`TutionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`TutorID`) ON DELETE CASCADE;

--
-- Constraints for table `learners`
--
ALTER TABLE `learners`
  ADD CONSTRAINT `learners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tuition_requests`
--
ALTER TABLE `tuition_requests`
  ADD CONSTRAINT `tuition_requests_learner_id_foreign` FOREIGN KEY (`learner_id`) REFERENCES `learners` (`LearnerID`) ON DELETE CASCADE;

--
-- Constraints for table `tutors`
--
ALTER TABLE `tutors`
  ADD CONSTRAINT `tutors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
