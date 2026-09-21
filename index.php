
<?php

session_start();

require_once "config/database.php";


// =====================================================
// GET CATEGORIES
// =====================================================

$categoryQuery = "
    SELECT id, category_name, image
    FROM flower_categories
    WHERE status = 'Active'
    ORDER BY id ASC
    LIMIT 6
";

$categoryResult = $conn->query($categoryQuery);


// =====================================================
// GET FEATURED / AVAILABLE FLOWERS
// =====================================================

$flowerQuery = "
    SELECT
        f.id,
        f.flower_name,
        f.description,
        f.price,
        f.stock_quantity,
        f.image,
        f.status,
        fc.category_name
    FROM flowers f
    LEFT JOIN flower_categories fc
        ON f.category_id = fc.id
    WHERE f.status = 'Available'
      AND f.stock_quantity > 0
    ORDER BY f.id DESC
    LIMIT 8
";

$flowerResult = $conn->query($flowerQuery);


// =====================================================
// CUSTOMER SESSION
// =====================================================

$isLoggedIn = isset($_SESSION["customer_id"]);


// =====================================================
// INCLUDE HEADER
// =====================================================

require_once "includes/header.php";

?>

<!-- =====================================================
     HERO SECTION
===================================================== -->

<section class="hero-section">

    <div class="hero-overlay"></div>

    <div class="container hero-container">

        <div class="hero-content">

            <span class="hero-small-text">
                ✿ FRESH FLOWERS • BEAUTIFUL MOMENTS
            </span>

            <h1>
                Flowers that make
                <span>every moment</span>
                special.
            </h1>

            <p>
                Discover beautifully arranged fresh flowers
                for birthdays, anniversaries, weddings and
                every meaningful moment in life.
            </p>

            <div class="hero-buttons">

                <a
                    href="products.php"
                    class="btn btn-primary"
                >
                    Shop Flowers
                    <span>→</span>
                </a>

                <a
                    href="#categories"
                    class="btn btn-light"
                >
                    Explore Collections
                </a>

            </div>


            <!-- TRUST ITEMS -->

            <div class="hero-trust">

                <div>
                    <strong>500+</strong>
                    <span>Happy Customers</span>
                </div>

                <div>
                    <strong>50+</strong>
                    <span>Flower Designs</span>
                </div>

                <div>
                    <strong>4.9/5</strong>
                    <span>Customer Rating</span>
                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     QUICK BENEFITS
===================================================== -->

<section class="benefits-section">

    <div class="container benefits-grid">


        <div class="benefit-card">

            <div class="benefit-icon">
                🌸
            </div>

            <div>
                <h3>Fresh Every Day</h3>

                <p>
                    Beautiful fresh flowers carefully selected
                    for every order.
                </p>
            </div>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                🚚
            </div>

            <div>
                <h3>Reliable Delivery</h3>

                <p>
                    We make sure your flowers reach their
                    destination safely and on time.
                </p>
            </div>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                💝
            </div>

            <div>
                <h3>Made With Love</h3>

                <p>
                    Every bouquet is prepared with care,
                    beauty and attention to detail.
                </p>
            </div>

        </div>


        <div class="benefit-card">

            <div class="benefit-icon">
                🔒
            </div>

            <div>
                <h3>Secure Ordering</h3>

                <p>
                    Your customer information and orders
                    are handled securely.
                </p>
            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     CATEGORY SECTION
===================================================== -->

<section
    class="categories-section"
    id="categories"
>

    <div class="container">

        <div class="section-heading">

            <div>

                <span class="section-label">
                    OUR COLLECTION
                </span>

                <h2>
                    Shop by flower type
                </h2>

            </div>

            <a
                href="categories.php"
                class="view-all-link"
            >
                View all categories →
            </a>

        </div>


        <div class="category-grid">

            <?php if ($categoryResult && $categoryResult->num_rows > 0): ?>

                <?php while ($category = $categoryResult->fetch_assoc()): ?>

                    <?php

                    $categoryImage = !empty($category["image"])
                        ? "images/" . $category["image"]
                        : "images/default-flower.jpg";

                    ?>

                    <a
                        href="products.php?category=<?php echo $category["id"]; ?>"
                        class="category-card"
                    >

                        <div class="category-image">

                            <img
                                src="<?php echo htmlspecialchars($categoryImage); ?>"
                                alt="<?php echo htmlspecialchars($category["category_name"]); ?>"
                            >

                            <div class="category-image-overlay"></div>

                            <div class="category-arrow">
                                →
                            </div>

                        </div>

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $category["category_name"]
                            );
                            ?>
                        </h3>

                    </a>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-message">
                    Flower categories will appear here.
                </div>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- =====================================================
     FEATURED FLOWERS
===================================================== -->

<section class="featured-section">

    <div class="container">

        <div class="section-heading centered">

            <span class="section-label">
                HANDPICKED FOR YOU
            </span>

            <h2>
                Our beautiful blooms
            </h2>

            <p>
                Find the perfect flowers to express what words
                sometimes cannot.
            </p>

        </div>


        <div class="flower-grid">

            <?php if ($flowerResult && $flowerResult->num_rows > 0): ?>

                <?php while ($flower = $flowerResult->fetch_assoc()): ?>

                    <?php

                    $flowerImage = !empty($flower["image"])
                        ? "images/" . $flower["image"]
                        : "images/default-flower.jpg";

                    $shortDescription = $flower["description"] ?? "";

                    if (strlen($shortDescription) > 70) {
                        $shortDescription =
                            substr($shortDescription, 0, 70) . "...";
                    }

                    ?>

                    <article class="flower-card">


                        <!-- IMAGE -->

                        <div class="flower-card-image">

                            <img
                                src="<?php echo htmlspecialchars($flowerImage); ?>"
                                alt="<?php echo htmlspecialchars($flower["flower_name"]); ?>"
                            >


                            <!-- BADGE -->

                            <span class="flower-badge">
                                Fresh
                            </span>


                            <!-- QUICK VIEW -->

                            <a
                                href="products.php"
                                class="quick-view"
                            >
                                View Details
                            </a>

                        </div>


                        <!-- CONTENT -->

                        <div class="flower-card-content">

                            <span class="flower-category">

                                <?php
                                echo htmlspecialchars(
                                    $flower["category_name"] ?? "Flowers"
                                );
                                ?>

                            </span>


                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $flower["flower_name"]
                                );
                                ?>

                            </h3>


                            <?php if (!empty($shortDescription)): ?>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $shortDescription
                                    );
                                    ?>
                                </p>

                            <?php endif; ?>


                            <div class="flower-card-bottom">

                                <div class="flower-price">

                                    <span>
                                        Rs.
                                    </span>

                                    <?php
                                    echo number_format(
                                        $flower["price"],
                                        2
                                    );
                                    ?>

                                </div>


                                <form
                                    action="cart/add-to-cart.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="flower_id"
                                        value="<?php echo $flower["id"]; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="quantity"
                                        value="1"
                                    >

                                    <button
                                        type="submit"
                                        class="add-cart-btn"
                                        title="Add to Cart"
                                    >
                                        +
                                    </button>

                                </form>

                            </div>

                        </div>

                    </article>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-message">
                    No flowers are currently available.
                </div>

            <?php endif; ?>

        </div>


        <div class="section-button">

            <a
                href="products.php"
                class="outline-btn"
            >
                Explore All Flowers
                <span>→</span>
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     EMOTIONAL PROMO SECTION
===================================================== -->

<section class="promo-section">

    <div class="container promo-container">

        <div class="promo-image">

            <img
                src="images/promo-flowers.jpg"
                alt="Beautiful flower bouquet"
            >

        </div>


        <div class="promo-content">

            <span class="section-label">
                MAKE SOMEONE SMILE
            </span>

            <h2>
                A little flower can
                say so much.
            </h2>

            <p>
                Whether it's a birthday, anniversary, thank you,
                congratulations or simply a reminder that you care,
                there's always a flower for the moment.
            </p>


            <div class="promo-points">

                <div>
                    <span>✓</span>
                    Beautifully arranged bouquets
                </div>

                <div>
                    <span>✓</span>
                    Fresh and carefully selected flowers
                </div>

                <div>
                    <span>✓</span>
                    Easy online ordering
                </div>

            </div>


            <a
                href="products.php"
                class="btn btn-primary"
            >
                Find Your Flowers
                <span>→</span>
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     HOW IT WORKS
===================================================== -->

<section class="how-section">

    <div class="container">

        <div class="section-heading centered">

            <span class="section-label">
                SIMPLE & EASY
            </span>

            <h2>
                Sending flowers is easy
            </h2>

            <p>
                From choosing your favourite flowers to
                making someone smile — we've made it simple.
            </p>

        </div>


        <div class="steps-grid">


            <div class="step-card">

                <div class="step-number">
                    01
                </div>

                <div class="step-icon">
                    🌷
                </div>

                <h3>
                    Choose Your Flowers
                </h3>

                <p>
                    Explore our collection and find flowers
                    that perfectly match your occasion.
                </p>

            </div>


            <div class="step-line"></div>


            <div class="step-card">

                <div class="step-number">
                    02
                </div>

                <div class="step-icon">
                    🛒
                </div>

                <h3>
                    Add to Your Cart
                </h3>

                <p>
                    Select your favourite blooms and choose
                    the quantity you need.
                </p>

            </div>


            <div class="step-line"></div>


            <div class="step-card">

                <div class="step-number">
                    03
                </div>

                <div class="step-icon">
                    💳
                </div>

                <h3>
                    Place Your Order
                </h3>

                <p>
                    Provide your delivery details and complete
                    your order easily.
                </p>

            </div>


            <div class="step-line"></div>


            <div class="step-card">

                <div class="step-number">
                    04
                </div>

                <div class="step-icon">
                    💐
                </div>

                <h3>
                    Make Someone Smile
                </h3>

                <p>
                    Your beautiful flowers are prepared and
                    delivered with care.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     OCCASION SECTION
===================================================== -->

<section class="occasion-section">

    <div class="container">

        <div class="occasion-heading">

            <span class="section-label">
                FOR EVERY OCCASION
            </span>

            <h2>
                There's always a reason
                to send flowers.
            </h2>

        </div>


        <div class="occasion-grid">


            <a
                href="products.php"
                class="occasion-card birthday"
            >

                <div class="occasion-overlay"></div>

                <div class="occasion-content">

                    <span>
                        🎂
                    </span>

                    <h3>
                        Birthdays
                    </h3>

                    <p>
                        Celebrate their special day.
                    </p>

                    <strong>
                        Shop Birthday Flowers →
                    </strong>

                </div>

            </a>


            <a
                href="products.php"
                class="occasion-card wedding"
            >

                <div class="occasion-overlay"></div>

                <div class="occasion-content">

                    <span>
                        💍
                    </span>

                    <h3>
                        Weddings
                    </h3>

                    <p>
                        Make their beautiful day unforgettable.
                    </p>

                    <strong>
                        Explore Wedding Flowers →
                    </strong>

                </div>

            </a>


            <a
                href="products.php"
                class="occasion-card love"
            >

                <div class="occasion-overlay"></div>

                <div class="occasion-content">

                    <span>
                        ❤️
                    </span>

                    <h3>
                        Love & Romance
                    </h3>

                    <p>
                        Say what your heart feels.
                    </p>

                    <strong>
                        Send Some Love →
                    </strong>

                </div>

            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     FINAL CTA
===================================================== -->

<section class="final-cta">

    <div class="container final-cta-content">

        <span>
            ✿ BLOOM HEAVEN
        </span>

        <h2>
            Let your feelings bloom.
        </h2>

        <p>
            Find something beautiful for someone special today.
        </p>

        <a
            href="products.php"
            class="btn btn-light"
        >
            Start Shopping
            <span>→</span>
        </a>

    </div>

</section>


<?php

require_once "includes/footer.php";

?>

