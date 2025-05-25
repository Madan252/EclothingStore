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
    <title>About Us - E-Clothing Store</title>
    <link rel="stylesheet" href="../assets/css/aboutus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <header class="topnav">
        <div class="logo">
            <i class="fas fa-tshirt"></i> <span>E-Clothing Store</span>
        </div>
        <nav class="topnav-menu">
            <a href="../Admin/Admindashboard.php" class="nav-link">Home</a>
            <a href="aboutus.php" class="nav-link active">About Us</a>
            <a href="#" class="nav-link">Contact Us</a>
            <a href="#" class="nav-link">Help</a>
            <a href="Logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="welcome-msg">
            <i class="fas fa-user-circle"></i> Welcome, <strong><?php echo htmlspecialchars($adminName); ?></strong>
        </div>
    </header>

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
