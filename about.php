<?php
require_once "includes/header.php";
?>

<style>
.about-page {
    padding: 70px 20px;
    background: #fffaf7;
    min-height: 70vh;
}

.about-container {
    max-width: 900px;
    margin: auto;
    text-align: center;
}

.about-container h1 {
    font-family: "Playfair Display", Georgia, serif;
    font-size: 42px;
    color: #294535;
    margin-bottom: 20px;
}

.about-container h1 span {
    color: #b85c70;
}

.about-container p {
    font-family: "DM Sans", Arial, sans-serif;
    font-size: 16px;
    line-height: 1.9;
    color: #706b68;
    margin-bottom: 20px;
}

.about-card {
    margin-top: 40px;
    padding: 35px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 10px 35px rgba(70, 45, 45, 0.08);
}

.about-card h2 {
    font-family: "Playfair Display", Georgia, serif;
    color: #294535;
    margin-bottom: 15px;
}
</style>

<main class="about-page">

    <div class="about-container">

        <h1>About <span>Bloom Heaven</span></h1>

        <p>
            Welcome to Bloom Heaven, your destination for
            beautiful flowers and unforgettable moments.
            We believe every occasion deserves a special touch
            of nature and love.
        </p>

        <p>
            Our flower booking system makes it easy to explore
            fresh floral arrangements, choose your favourite
            blooms, and place your order from the comfort
            of your home.
        </p>

        <div class="about-card">

            <h2>Our Mission</h2>

            <p>
                Our mission is to make flower shopping simple,
                convenient, and enjoyable while helping you
                celebrate life's beautiful moments with flowers.
            </p>

        </div>

    </div>

</main>

<?php
require_once "includes/footer.php";
?>