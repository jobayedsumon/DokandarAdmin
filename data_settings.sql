-- -------------------------------------------------------------
-- TablePlus 5.6.6(520)
--
-- https://tableplus.com/
--
-- Database: dokandar
-- Generation Time: 2024-02-09 23:34:06.7550
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `data_settings`;
CREATE TABLE `data_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

INSERT INTO `data_settings` (`id`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'admin_login_url', 'admin', 'login_admin', '2023-06-11 20:34:59', '2023-06-11 20:34:59'),
(2, 'admin_employee_login_url', 'admin-employee', 'login_admin_employee', '2023-06-11 20:34:59', '2023-06-11 20:34:59'),
(3, 'store_login_url', 'store', 'login_store', '2023-06-11 20:34:59', '2023-06-11 20:34:59'),
(4, 'store_employee_login_url', 'store-employee', 'login_store_employee', '2023-06-11 20:34:59', '2023-06-11 20:34:59'),
(5, 'fixed_header_title', 'Manage Your  Daily Life in one platform', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(6, 'fixed_header_sub_title', 'More than just a reliable  eCommerce platform', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(7, 'fixed_module_title', 'Your eCommerce venture starts here !', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(8, 'fixed_module_sub_title', 'Enjoy all services in one platform', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(9, 'fixed_referal_title', 'Earn point by', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(10, 'fixed_referal_sub_title', 'Refer Your Friend', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(11, 'fixed_newsletter_title', 'Sign Up to Our Newsletter', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(12, 'fixed_newsletter_sub_title', 'Receive Latest News, Updates and Many Other News Every Week', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(13, 'fixed_footer_article_title', '6amMart is a complete package!  It\'s time to empower your multivendor online business with  powerful features!', 'admin_landing_page', '2023-06-11 21:06:27', '2023-06-11 21:06:27'),
(14, 'feature_title', 'Remarkable Features that You Can Count!', 'admin_landing_page', '2023-06-11 21:14:25', '2023-06-11 21:14:25'),
(15, 'feature_short_description', 'Jam-packed with outstanding features to elevate your online ordering and delivery easier, and smarter than ever before. It\'s time to empower your multivendor online business with 6amMart\'s powerful features!', 'admin_landing_page', '2023-06-11 21:14:25', '2023-06-11 21:14:25'),
(16, 'earning_title', 'Earn Money', 'admin_landing_page', '2023-06-11 21:26:01', '2023-06-11 21:26:01'),
(17, 'earning_sub_title', 'Earn money  by using different platform', 'admin_landing_page', '2023-06-11 21:26:01', '2023-06-11 21:26:01'),
(18, 'earning_seller_image', '2023-08-16-64dcaa6634ab5.png', 'admin_landing_page', '2023-06-11 21:27:29', '2023-08-16 05:52:22'),
(19, 'seller_app_earning_links', '{\"playstore_url_status\":null,\"playstore_url\":null,\"apple_store_url_status\":null,\"apple_store_url\":null}', 'admin_landing_page', NULL, NULL),
(20, 'earning_delivery_image', '2023-08-16-64dcaa7ba5b80.png', 'admin_landing_page', '2023-06-11 21:28:48', '2023-08-16 05:52:43'),
(21, 'dm_app_earning_links', '{\"playstore_url_status\":null,\"playstore_url\":null,\"apple_store_url_status\":null,\"apple_store_url\":null}', 'admin_landing_page', NULL, NULL),
(22, 'why_choose_title', 'What so Special About 6amMart ?', 'admin_landing_page', '2023-06-11 21:30:30', '2023-06-11 21:32:08'),
(23, 'counter_section', '{\"app_download_count_numbers\":\"300\",\"seller_count_numbers\":\"85\",\"deliveryman_count_numbers\":\"150\",\"customer_count_numbers\":\"10000\",\"status\":\"1\"}', 'admin_landing_page', NULL, NULL),
(24, 'download_user_app_title', 'Let’s  Manage', 'admin_landing_page', '2023-06-11 21:38:17', '2023-06-11 21:38:17'),
(25, 'download_user_app_sub_title', 'Your business  Smartly or Earn.', 'admin_landing_page', '2023-06-11 21:38:17', '2023-06-11 21:38:17'),
(26, 'download_user_app_image', '2023-08-16-64dcaab460ac2.png', 'admin_landing_page', '2023-06-11 21:38:17', '2023-08-16 05:53:40'),
(27, 'download_user_app_links', '{\"playstore_url_status\":\"1\",\"playstore_url\":\"https:\\/\\/play.google.com\\/store\\/apps\\/details?id=com.sixamtech.sixam_mart_store_app\",\"apple_store_url_status\":\"1\",\"apple_store_url\":\"https:\\/\\/www.apple.com\\/app-store\"}', 'admin_landing_page', NULL, NULL),
(28, 'testimonial_title', 'People Who Shared Love with us?', 'admin_landing_page', '2023-06-11 21:42:04', '2023-06-11 21:42:04'),
(29, 'contact_us_title', 'Contact Us', 'admin_landing_page', '2023-06-11 21:53:22', '2023-06-11 21:53:22'),
(30, 'contact_us_sub_title', 'Any question or remarks? Just write us a message!', 'admin_landing_page', '2023-06-11 21:53:22', '2023-06-11 21:53:22'),
(31, 'contact_us_image', '2023-08-16-64dcab0c7b434.png', 'admin_landing_page', '2023-06-11 21:53:23', '2023-08-16 05:55:08'),
(32, 'refund_policy_status', '1', 'admin_landing_page', '2023-06-12 02:10:58', '2023-06-12 02:10:58'),
(33, 'refund_policy', NULL, 'admin_landing_page', '2023-06-12 02:10:59', '2023-06-12 02:10:59'),
(34, 'header_title', '$Your e-Commerce!$', 'react_landing_page', '2023-06-12 22:30:53', '2023-06-13 01:41:19'),
(35, 'header_sub_title', 'Venture Starts Here', 'react_landing_page', '2023-06-12 22:30:53', '2023-06-12 23:55:14'),
(36, 'header_tag_line', 'More than just a reliable $eCommerce$ platform', 'react_landing_page', '2023-06-12 22:30:53', '2023-06-12 23:45:24'),
(37, 'header_icon', '2023-08-16-64dcac0088f46.png', 'react_landing_page', '2023-06-12 22:30:53', '2023-08-16 05:59:12'),
(38, 'header_banner', '2023-08-20-64e1e31738bbc.png', 'react_landing_page', '2023-06-12 22:30:53', '2023-08-20 04:55:35'),
(39, 'company_title', '$6amMart$', 'react_landing_page', '2023-06-12 22:35:07', '2023-06-12 23:46:19'),
(40, 'company_sub_title', 'is Best Delivery Service Near You', 'react_landing_page', '2023-06-12 22:35:07', '2023-06-12 22:35:07'),
(41, 'company_description', '6amMart is a one-stop shop for all your daily necessities. You can shop for groceries, and pharmacy items, order food, and send important parcels from one place to another from the comfort of your home.', 'react_landing_page', '2023-06-12 22:35:07', '2023-06-12 22:35:07'),
(42, 'company_button_name', 'Order Now', 'react_landing_page', '2023-06-12 22:35:07', '2023-06-12 23:46:52'),
(43, 'company_button_url', 'https://6ammart-react.6amtech.com/', 'react_landing_page', '2023-06-12 22:35:07', '2023-06-12 22:35:07'),
(44, 'download_user_app_title', 'Complete Multipurpose eBusiness Solution', 'react_landing_page', '2023-06-12 22:40:30', '2023-06-12 22:40:30'),
(45, 'download_user_app_sub_title', '6amMart is a Laravel and Flutter Framework-based multi-vendor food, grocery, eCommerce, parcel, and pharmacy delivery system. It has six modules to cover all your business function', 'react_landing_page', '2023-06-12 22:40:30', '2023-06-12 22:40:30'),
(46, 'download_user_app_image', NULL, 'react_landing_page', '2023-06-12 22:40:30', '2023-06-12 22:40:30'),
(47, 'download_user_app_links', '{\"playstore_url_status\":\"1\",\"playstore_url\":\"https:\\/\\/play.google.com\\/store\\/\",\"apple_store_url_status\":\"1\",\"apple_store_url\":\"https:\\/\\/www.apple.com\\/app-store\\/\"}', 'react_landing_page', NULL, NULL),
(48, 'earning_title', 'Let’s Start Earning with $6amMart$', 'react_landing_page', '2023-06-12 22:43:22', '2023-06-12 22:43:22'),
(49, 'earning_sub_title', 'Join our online marketplace revolution and boost your income.', 'react_landing_page', '2023-06-12 22:43:22', '2023-06-12 22:43:22'),
(50, 'earning_seller_title', 'Become a Seller', 'react_landing_page', '2023-06-12 22:45:07', '2023-06-12 22:45:07'),
(51, 'earning_seller_sub_title', 'Register as seller & open shop in 6amMart to start your business', 'react_landing_page', '2023-06-12 22:45:07', '2023-06-12 22:45:07'),
(52, 'earning_seller_button_name', 'Register', 'react_landing_page', '2023-06-12 22:45:07', '2023-06-12 22:45:07'),
(53, 'earning_seller_button_url', 'https://6ammart-admin.6amtech.com/store/apply', 'react_landing_page', '2023-06-12 22:45:07', '2023-06-12 22:45:07'),
(54, 'earning_dm_title', 'Become a $Delivery Man$', 'react_landing_page', '2023-06-12 22:45:55', '2023-06-12 23:53:01'),
(55, 'earning_dm_sub_title', 'Register as delivery man and earn money', 'react_landing_page', '2023-06-12 22:45:55', '2023-06-12 22:45:55'),
(56, 'earning_dm_button_name', 'Register', 'react_landing_page', '2023-06-12 22:45:55', '2023-06-12 22:45:55'),
(57, 'earning_dm_button_url', 'https://6ammart-admin.6amtech.com/deliveryman/apply', 'react_landing_page', '2023-06-12 22:45:55', '2023-06-12 22:45:55'),
(58, 'promotion_banner', '[{\"img\":\"2023-08-16-64dcac89cd0fa.png\"},{\"img\":\"2023-08-16-64dcac93a324a.png\"},{\"img\":\"2023-08-16-64dcad5a24940.png\"}]', 'react_landing_page', NULL, '2023-08-16 06:01:02'),
(59, 'business_title', '$Let’s$', 'react_landing_page', '2023-06-12 22:52:29', '2023-06-12 22:52:29'),
(60, 'business_sub_title', 'Manage your business  Smartly', 'react_landing_page', '2023-06-12 22:52:29', '2023-06-12 23:54:18'),
(61, 'business_image', '2023-08-16-64dcad66585e9.png', 'react_landing_page', '2023-06-12 22:52:29', '2023-08-16 06:05:10'),
(62, 'download_business_app_links', '{\"seller_playstore_url_status\":\"1\",\"seller_playstore_url\":\"https:\\/\\/play.google.com\\/store\",\"seller_appstore_url_status\":\"1\",\"seller_appstore_url\":\"https:\\/\\/www.apple.com\\/app-store\\/\",\"dm_playstore_url_status\":\"1\",\"dm_playstore_url\":\"https:\\/\\/play.google.com\\/store\",\"dm_appstore_url_status\":\"1\",\"dm_appstore_url\":\"https:\\/\\/www.apple.com\\/app-store\\/\"}', 'react_landing_page', NULL, NULL),
(63, 'testimonial_title', 'We $satisfied$ some Customer & Restaurant Owners', 'react_landing_page', '2023-06-12 22:53:04', '2023-06-12 22:53:04'),
(64, 'fixed_promotional_banner', '2023-08-16-64dcadedb4fac.png', 'react_landing_page', '2023-06-12 23:18:24', '2023-08-16 06:07:25'),
(65, 'fixed_footer_description', 'Connect with our social media and other sites to keep up to date', 'react_landing_page', '2023-06-12 23:21:12', '2023-06-12 23:21:12'),
(66, 'fixed_newsletter_title', 'Join Us!', 'react_landing_page', '2023-06-12 23:23:45', '2023-06-12 23:23:45'),
(67, 'fixed_newsletter_sub_title', 'Subscribe to our weekly newsletter and be a part of our journey to self discovery and love.', 'react_landing_page', '2023-06-12 23:23:45', '2023-06-12 23:23:45'),
(68, 'fixed_header_title', '6amMart', 'flutter_landing_page', '2023-06-12 23:31:35', '2023-06-12 23:31:35'),
(69, 'fixed_header_sub_title', 'More than just reliable eCommerce platform', 'flutter_landing_page', '2023-06-12 23:31:35', '2023-06-12 23:32:30'),
(70, 'fixed_header_image', '2023-08-16-64dcae3571b9a.png', 'flutter_landing_page', '2023-06-12 23:31:35', '2023-08-16 06:08:37'),
(71, 'fixed_location_title', 'Choose your location', 'flutter_landing_page', '2023-06-12 23:35:02', '2023-06-12 23:35:02'),
(72, 'fixed_module_title', 'Your eCommerce venture starts here !', 'flutter_landing_page', '2023-06-12 23:37:29', '2023-06-12 23:37:29'),
(73, 'fixed_module_sub_title', 'Enjoy all services in one platform', 'flutter_landing_page', '2023-06-12 23:37:29', '2023-06-12 23:37:29'),
(74, 'join_seller_title', 'Become a Seller', 'flutter_landing_page', '2023-06-13 00:12:56', '2023-06-13 00:12:56'),
(75, 'join_seller_sub_title', 'Registered as a seller and open shop for start your business', 'flutter_landing_page', '2023-06-13 00:12:56', '2023-06-13 00:12:56'),
(76, 'join_seller_button_name', 'Register', 'flutter_landing_page', '2023-06-13 00:12:56', '2023-06-13 00:12:56'),
(77, 'join_seller_button_url', 'https://6ammart-admin.6amtech.com/store/apply', 'flutter_landing_page', '2023-06-13 00:12:56', '2023-06-13 00:12:56'),
(78, 'join_delivery_man_title', 'Join as  Deliveryman', 'flutter_landing_page', '2023-06-13 00:16:03', '2023-06-13 00:16:03'),
(79, 'join_delivery_man_sub_title', 'Registered as a deliveryman and earn money', 'flutter_landing_page', '2023-06-13 00:16:03', '2023-06-13 00:16:03'),
(80, 'join_delivery_man_button_name', 'Register', 'flutter_landing_page', '2023-06-13 00:16:03', '2023-06-13 00:16:03'),
(81, 'join_delivery_man_button_url', 'https://6ammart-admin.6amtech.com/deliveryman/apply', 'flutter_landing_page', '2023-06-13 00:16:03', '2023-06-13 00:16:03'),
(82, 'download_user_app_title', 'Download app and enjoy more!', 'flutter_landing_page', '2023-06-13 00:17:56', '2023-06-13 00:17:56'),
(83, 'download_user_app_sub_title', 'Download app from', 'flutter_landing_page', '2023-06-13 00:17:56', '2023-06-13 00:17:56'),
(84, 'download_user_app_image', '2023-08-16-64dcae82675b2.png', 'flutter_landing_page', '2023-06-13 00:17:56', '2023-08-16 06:09:54'),
(85, 'download_user_app_links', '{\"playstore_url_status\":\"1\",\"playstore_url\":\"https:\\/\\/play.google.com\\/store\\/\",\"apple_store_url_status\":\"1\",\"apple_store_url\":\"https:\\/\\/www.apple.com\\/app-store\\/\"}', 'flutter_landing_page', NULL, NULL);


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;