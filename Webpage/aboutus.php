<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: Adminlogin.php");
    exit();  
}
$adminName = $_SESSION['admin_name'] ?? $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title> </title>
    <link rel="stylesheet" href="../Webpage/css/Aboutus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

     <?php include '../Webpage/Home/header.php'; ?>

    <main class="main-content">
        <section class="project-description-box">
            <h1>About E-Clothing</h1>
            <p>
                E-Clothing is a modern e-commerce platform tailored for fashion and clothing businesses. Developed as part of our Bachelor of Computer Applications (BCA) project, this web application offers a seamless online shopping experience with intuitive product browsing, secure order management, and a robust admin dashboard.
            </p>
            <p>
                Key features include dynamic order tracking, responsive UI design, user and admin roles, and category-based product organization. The system is built with HTML, CSS, PHP, and MySQL, ensuring both functionality and scalability.
            </p>
            <p>
                Our goal was to create an impactful and user-friendly solution that showcases our technical skills in full-stack development while addressing real-world business needs.
            </p>
        </section>

        <section class="info-section">
            <div class="info-box vision-box">
                <h2>Our Vision</h2>
                <p>
                    To redefine fashion retail by merging technology, sustainability, and personalization—making ethical, intelligent clothing choices accessible to everyone, everywhere.
                </p>
            </div>
            <div class="info-box mission-box">
                <h2>Our Mission</h2>
                <p>
                    To provide a platform for fancy stores that helps them increase the number of customers both offline and online.
                </p>
            </div>
            <div class="info-box goals-box">
                <h2>Our Goals</h2>
                <ul>
                    <li>Provide clothing at very marginal costs.</li>
                    <li>Offer clothes for rent on a weekly basis.</li>
                </ul>
            </div>
        </section>

        <h2 class="team-title">Meet Our Team</h2>
        <div class="team-container">
            <div class="team-member">
                <img src="../assets/image/team/dipa.jpg" alt="Dipa Bist" class="profile-img">
                <h3>Dipa Bist</h3>
                <p>Backend & Database Management</p>
                <p>Project Conceptualizer</p>
                <p>Brand Ambassador</p>
            </div>
            <div class="team-member">
                <img src="../assets/image/team/laxmi.jpg" alt="Laxmi Dadal" class="profile-img">
                <h3>Laxmi Dadal</h3>
                <p>Brand Ambassador</p>
                <p>Project Documentation & Deployment</p>
            </div>
            <div class="team-member">
                <img src="../assets/image/team/madan.jpg" alt="Madan Raj Joshi" class="profile-img">
                <h3>Madan Raj Joshi</h3>
                <p>Frontend Development</p>
                <p>Full-Stack Development & UI Design</p>
                <p>Web Developer</p>
            </div>
            <div class="team-member">
                <img src="../assets/image/team/subash.jpg" alt="Subash Chand" class="profile-img">
                <h3>Subash Chand</h3>
                <p>Frontend Development</p>
                <p>Full-Stack Development & UI Design</p>
                <p>Senior Web Developer</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2025 E-Clothing Store. All Rights Reserved.</p>
    </footer>
</body>
</html>
