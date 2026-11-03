-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2026 at 12:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ebook_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` tinyint(1) NOT NULL DEFAULT 2,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `email`, `password`, `role_id`, `created_at`) VALUES
(1, 'Admin User', 'admin@ebookhub.com', 'admin123', 1, '2026-07-14 21:52:04'),
(6, 'Maheer Feroz', 'maheer@example.com', 'maheer123', 2, '2026-07-15 02:53:57');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `format` enum('PDF','Hard copy') DEFAULT 'PDF',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weight` decimal(5,2) DEFAULT 0.50,
  `image` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_path` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `format`, `price`, `shipping_charge`, `weight`, `image`, `description`, `pdf_path`, `is_featured`, `created_at`) VALUES
(1, 'The Alchemist', 'Paulo Coelho', 'Novels', 'PDF', 450.00, 0.00, 0.00, NULL, 'A timeless story about following your dreams and discovering your destiny.', NULL, 1, '2026-07-14 21:52:04'),
(2, 'Atomic Habits', 'James Clear', 'Education', 'PDF', 650.00, 0.00, 0.00, NULL, 'Tiny changes, remarkable results — the definitive guide to building good habits.', NULL, 1, '2026-07-14 21:52:04'),
(3, 'Rich Dad Poor Dad', 'Robert Kiyosaki', 'Business', 'Hard copy', 550.00, 150.00, 0.40, NULL, 'What the rich teach their kids about money that the poor and middle class do not.', NULL, 1, '2026-07-14 21:52:04'),
(4, 'Sapiens', 'Yuval Noah Harari', 'History', 'PDF', 750.00, 0.00, 0.00, NULL, 'A brief history of humankind — from Stone Age to the modern era.', NULL, 1, '2026-07-14 21:52:04'),
(5, 'Batman: The Killing Joke', 'Alan Moore', 'Comics', 'Hard copy', 300.00, 120.00, 0.30, NULL, 'One of the most acclaimed Batman stories ever written.', NULL, 1, '2026-07-14 21:52:04'),
(6, 'A Brief History of Time', 'Stephen Hawking', 'Science', 'PDF', 500.00, 0.00, 0.00, NULL, 'An exploration of black holes, the Big Bang, and the nature of time.', NULL, 1, '2026-07-14 21:52:04'),
(7, 'Think and Grow Rich', 'Napoleon Hill', 'Business', 'PDF', 400.00, 0.00, 0.00, NULL, 'The classic masterpiece on achieving success and building wealth.', NULL, 1, '2026-07-14 21:52:04'),
(8, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Novels', 'Hard copy', 350.00, 130.00, 0.35, NULL, 'A story of wealth, love, and the American Dream in the roaring twenties.', NULL, 1, '2026-07-14 21:52:04'),
(9, 'Dune', 'Frank Herbert', 'Fiction', 'PDF', 600.00, 0.00, 0.00, NULL, 'Epic science fiction spanning feudal interstellar societies and a desert planet.', NULL, 1, '2026-07-14 21:52:04'),
(10, 'Spider-Man: Blue', 'Jeph Loeb', 'Comics', 'Hard copy', 280.00, 110.00, 0.25, NULL, 'A beautiful, heartfelt tribute to Peter Parker and his first great love.', NULL, 0, '2026-07-14 21:52:04'),
(11, 'The Psychology of Money', 'Morgan Housel', 'Business', 'PDF', 500.00, 0.00, 0.00, NULL, 'Timeless lessons on wealth, greed, and happiness through 19 short stories.', NULL, 0, '2026-07-14 21:52:04'),
(12, 'To Kill a Mockingbird', 'Harper Lee', 'Novels', 'PDF', 420.00, 0.00, 0.00, NULL, 'A Pulitzer Prize–winning story of racial injustice and moral growth.', NULL, 0, '2026-07-14 21:52:04'),
(13, 'Cosmos', 'Carl Sagan', 'Science', 'Hard copy', 480.00, 140.00, 0.50, NULL, 'A personal voyage through the universe by one of its greatest guides.', NULL, 0, '2026-07-14 21:52:04'),
(14, 'Ikigai', 'Héctor García', 'Education', 'PDF', 380.00, 0.00, 0.00, NULL, 'The Japanese secret to a long and happy life — finding your reason for being.', NULL, 0, '2026-07-14 21:52:04'),
(15, 'Steve Jobs', 'Walter Isaacson', 'Biography', 'PDF', 700.00, 0.00, 0.00, NULL, 'The exclusive biography of the visionary co-founder of Apple.', NULL, 0, '2026-07-14 21:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `dealers`
--

CREATE TABLE `dealers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dealers`
--

INSERT INTO `dealers` (`id`, `name`, `address`, `phone`, `city`, `email`) VALUES
(1, 'Kitab Mahal', 'Urdu Bazar, Karachi', '+92 21 3456 7890', 'Karachi', NULL),
(2, 'National Book Centre', 'Mall Road, Lahore', '+92 42 3456 7890', 'Lahore', NULL),
(3, 'Al-Maktaba', 'G-9 Markaz, Islamabad', '+92 51 2345 6789', 'Islamabad', NULL),
(4, 'Book Corner', 'Saddar Bazar, Rawalpindi', '+92 51 3456 7890', 'Rawalpindi', NULL),
(5, 'Premier Book Shop', 'Zainab Market, Karachi', '+92 21 2345 6789', 'Karachi', NULL),
(6, 'Reading World', 'Liberty Market, Lahore', '+92 42 2345 6789', 'Lahore', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competition_type` varchar(50) NOT NULL DEFAULT 'story',
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `essay_submissions`
--

CREATE TABLE `essay_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `essay_text` longtext NOT NULL,
  `time_taken` int(11) DEFAULT 0 COMMENT 'Seconds taken to write',
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `essay_submissions`
--

INSERT INTO `essay_submissions` (`id`, `user_id`, `topic_id`, `essay_text`, `time_taken`, `submitted_at`) VALUES
(1, 6, 4, '# Education Systems: Tradition vs Innovation\r\n\r\nEducation is one of the most important foundations of a successful society. It helps people develop knowledge, skills, and values that prepare them for the future. Over time, education systems have changed significantly. Traditional education focuses on classroom learning, textbooks, discipline, and teacher-centered instruction, while innovative education emphasizes technology, creativity, collaboration, and student-centered learning. Both systems have their strengths and weaknesses, and finding the right balance is essential.\r\n\r\nTraditional education has been used for many generations. In this system, teachers play the central role by delivering lectures, assigning homework, and evaluating students through examinations. This method provides a structured learning environment, encourages discipline, and ensures that students build strong academic foundations. Subjects such as mathematics, science, and languages are taught systematically, helping students develop essential knowledge and critical thinking skills.\r\n\r\nHowever, traditional education also has some limitations. It often focuses on memorization rather than understanding and practical application. Students may have fewer opportunities to explore their creativity, solve real-world problems, or learn at their own pace. As a result, some learners may lose interest or struggle to adapt to modern workplace requirements.\r\n\r\nInnovative education, on the other hand, uses technology and modern teaching methods to make learning more engaging and effective. Digital classrooms, online courses, virtual laboratories, artificial intelligence, and interactive learning platforms allow students to access information anytime and anywhere. This approach encourages collaboration, creativity, communication, and problem-solving skills. Students become active participants in their learning instead of simply listening to lectures.\r\n\r\nDespite its advantages, innovative education also faces challenges. Not all schools have access to advanced technology or reliable internet connections. Some students may become distracted by digital devices, and excessive screen time can affect their health and concentration. In addition, teachers need proper training to use modern educational tools effectively.\r\n\r\nThe future of education should not rely entirely on either traditional or innovative methods. Instead, combining the strengths of both systems can create the best learning experience. Traditional teaching provides discipline, strong academic foundations, and personal interaction, while innovative methods improve engagement, flexibility, and practical skills. Together, they prepare students to succeed in a rapidly changing world.\r\n\r\nIn conclusion, both traditional and innovative education systems play valuable roles in shaping future generations. While traditional education offers stability and structure, innovation brings creativity and modern learning opportunities. A balanced approach that integrates both methods will help students become knowledgeable, skilled, and adaptable individuals who are ready to face the challenges of the future.', 58, '2026-07-15 03:09:23');

-- --------------------------------------------------------

--
-- Table structure for table `essay_topics`
--

CREATE TABLE `essay_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `essay_topics`
--

INSERT INTO `essay_topics` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'The Future of Artificial Intelligence', 'Discuss how AI will reshape human society, work, and relationships over the next 50 years.', '2026-07-14 21:52:04'),
(2, 'Climate Change: A Call to Action', 'Explore the causes, consequences, and potential solutions to the global climate crisis.', '2026-07-14 21:52:04'),
(3, 'The Importance of Reading in the Digital Age', 'Argue for or against the importance of reading physical books in a world dominated by digital media.', '2026-07-14 21:52:04'),
(4, 'Education Systems: Tradition vs Innovation', 'Compare traditional education methods with modern, technology-driven approaches to learning.', '2026-07-14 21:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `items` text NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(100) DEFAULT 'Credit Card',
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `items`, `total`, `payment_method`, `paid`, `status`, `order_date`, `full_name`, `email`, `phone`, `address`, `city`, `notes`) VALUES
(5, 6, 'Sapiens (x1)', 750.00, 'Credit Card', 1, 'Delivered', '2026-07-15 03:00:50', 'Maheer Feroz', 'maheer@example.com', '+923258475274', 'PIB COLONY NEAR JAIL ROAD', 'KARACHI', NULL),
(6, 6, 'Cosmos (x2), To Kill a Mockingbird (x1)', 1660.00, 'Credit Card', 0, 'Pending', '2026-07-15 03:06:10', 'Maheer Feroz', 'maheer@example.com', '+923258475274', 'FOOD STREET\r\nBUILDING', 'KARACHI', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `story_submissions`
--

CREATE TABLE `story_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_order_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_order_summary` (
`order_id` int(11)
,`customer_name` varchar(255)
,`customer_email` varchar(255)
,`items` text
,`total` decimal(10,2)
,`payment_method` varchar(100)
,`paid` tinyint(1)
,`status` varchar(50)
,`order_date` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE `winners` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL COMMENT 'References essay_topics.id',
  `rank` tinyint(1) DEFAULT 1,
  `prize` varchar(255) DEFAULT NULL,
  `announced_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_order_summary`
--
DROP TABLE IF EXISTS `v_order_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_order_summary`  AS SELECT `o`.`id` AS `order_id`, `a`.`name` AS `customer_name`, `a`.`email` AS `customer_email`, `o`.`items` AS `items`, `o`.`total` AS `total`, `o`.`payment_method` AS `payment_method`, `o`.`paid` AS `paid`, `o`.`status` AS `status`, `o`.`order_date` AS `order_date` FROM (`orders` `o` left join `accounts` `a` on(`o`.`user_id` = `a`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealers`
--
ALTER TABLE `dealers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `essay_submissions`
--
ALTER TABLE `essay_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `topic_id_idx` (`topic_id`);

--
-- Indexes for table `essay_topics`
--
ALTER TABLE `essay_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `story_submissions`
--
ALTER TABLE `story_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `winners`
--
ALTER TABLE `winners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `competition_id_idx` (`competition_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dealers`
--
ALTER TABLE `dealers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `essay_submissions`
--
ALTER TABLE `essay_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `essay_topics`
--
ALTER TABLE `essay_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `story_submissions`
--
ALTER TABLE `story_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winners`
--
ALTER TABLE `winners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `entries`
--
ALTER TABLE `entries`
  ADD CONSTRAINT `fk_entry_user` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `essay_submissions`
--
ALTER TABLE `essay_submissions`
  ADD CONSTRAINT `fk_essay_topic` FOREIGN KEY (`topic_id`) REFERENCES `essay_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_essay_user` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `story_submissions`
--
ALTER TABLE `story_submissions`
  ADD CONSTRAINT `fk_story_user` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `winners`
--
ALTER TABLE `winners`
  ADD CONSTRAINT `fk_winner_comp` FOREIGN KEY (`competition_id`) REFERENCES `essay_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_winner_user` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
