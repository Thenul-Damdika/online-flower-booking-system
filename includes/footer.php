
<!-- =========================
     BLOOM HEAVEN FOOTER
========================= -->

<footer class="site-footer">

    <!-- Newsletter Section -->

    <div class="footer-newsletter">

        <div class="container newsletter-content">

            <div class="newsletter-text">

                <span class="newsletter-label">
                    STAY IN BLOOM
                </span>

                <h2>
                    Get beautiful flowers<br>
                    delivered to your inbox.
                </h2>

                <p>
                    Subscribe for fresh flower ideas, special offers,
                    seasonal collections and more.
                </p>

            </div>


            <form
                class="newsletter-form"
                action="#"
                method="POST"
            >

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email address"
                    required
                >

                <button type="submit">
                    Subscribe
                </button>

            </form>

        </div>

    </div>


    <!-- Main Footer -->

    <div class="footer-main">

        <div class="container footer-grid">


            <!-- BRAND -->

            <div class="footer-brand">

                <a
                    href="/online-flower-booking-system/index.php"
                    class="footer-logo"
                >

                    <span class="footer-logo-icon">
                        ✿
                    </span>

                    <span>
                        <strong>Bloom</strong>
                        Heaven
                    </span>

                </a>


                <p class="footer-description">

                    Bringing beautiful flowers and meaningful
                    moments closer to you. Choose your favourite
                    blooms and let us make every occasion special.

                </p>


                <!-- Social Media -->

                <div class="footer-socials">

                    <a
                        href="#"
                        aria-label="Facebook"
                        title="Facebook"
                    >
                        f
                    </a>

                    <a
                        href="#"
                        aria-label="Instagram"
                        title="Instagram"
                    >
                        ◎
                    </a>

                    <a
                        href="#"
                        aria-label="TikTok"
                        title="TikTok"
                    >
                        ♪
                    </a>

                    <a
                        href="#"
                        aria-label="WhatsApp"
                        title="WhatsApp"
                    >
                        ◉
                    </a>

                </div>

            </div>


            <!-- QUICK LINKS -->

            <div class="footer-column">

                <h3>
                    Quick Links
                </h3>

                <ul>

                    <li>
                        <a href="/online-flower-booking-system/index.php">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="/online-flower-booking-system/products.php">
                            Shop Flowers
                        </a>
                    </li>

                    <li>
                        <a href="/online-flower-booking-system/categories.php">
                            Categories
                        </a>
                    </li>

                    <li>
                        <a href="/online-flower-booking-system/about.php">
                            About Us
                        </a>
                    </li>

                    <li>
                        <a href="/online-flower-booking-system/contact.php">
                            Contact Us
                        </a>
                    </li>

                </ul>

            </div>


            <!-- CUSTOMER SERVICE -->

            <div class="footer-column">

                <h3>
                    Customer Care
                </h3>

                <ul>

                    <li>
                        <a href="/online-flower-booking-system/cart/cart.php">
                            My Cart
                        </a>
                    </li>

                    <li>
                        <a href="/online-flower-booking-system/customer/profile.php">
                            My Account
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Delivery Information
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Order Information
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Privacy Policy
                        </a>
                    </li>

                </ul>

            </div>


            <!-- CONTACT -->

            <div class="footer-column footer-contact">

                <h3>
                    Contact Us
                </h3>


                <div class="contact-item">

                    <span class="contact-icon">
                        📍
                    </span>

                    <p>
                        Bloom Heaven,<br>
                        Colombo, Sri Lanka
                    </p>

                </div>


                <div class="contact-item">

                    <span class="contact-icon">
                        📞
                    </span>

                    <a href="tel:+94112345678">
                        +94 11 234 5678
                    </a>

                </div>


                <div class="contact-item">

                    <span class="contact-icon">
                        ✉
                    </span>

                    <a href="mailto:hello@bloomheaven.lk">
                        hello@bloomheaven.lk
                    </a>

                </div>


                <div class="contact-item">

                    <span class="contact-icon">
                        🕐
                    </span>

                    <p>
                        Mon – Sat: 8:00 AM – 7:00 PM
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- Bottom Footer -->

    <div class="footer-bottom">

        <div class="container footer-bottom-content">

            <p>
                © <?php echo date("Y"); ?>
                Bloom Heaven.
                All Rights Reserved.
            </p>

            <p>
                Made with <span class="heart">♥</span>
                for flower lovers.
            </p>

        </div>

    </div>

</footer>


<!-- =========================
     BACK TO TOP BUTTON
========================= -->

<button
    id="backToTop"
    class="back-to-top"
    type="button"
    aria-label="Back to top"
>
    ↑
</button>


<!-- =========================
     FOOTER JAVASCRIPT
========================= -->

<script>

const backToTop =
    document.getElementById("backToTop");


window.addEventListener("scroll", function() {

    if (window.scrollY > 400) {

        backToTop.classList.add("show");

    } else {

        backToTop.classList.remove("show");

    }

});


backToTop.addEventListener("click", function() {

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

});

</script>


</body>
</html>

