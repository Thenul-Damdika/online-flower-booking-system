<?php
session_start();
require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Fetch Flower Categories
|--------------------------------------------------------------------------
*/

$categoryStmt = $conn->prepare(
    "SELECT id, category_name, image
     FROM flower_categories
     ORDER BY id ASC"
);

$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();

require_once "includes/header.php";
?>

<style>
    /* =====================================================
       CATEGORIES PAGE
    ===================================================== */

    .categories-page {
        min-height: 70vh;
        padding: 70px 20px 80px;
        background: var(--cream, #fffaf7);
    }

    .categories-container {
        width: min(1200px, 100%);
        margin: 0 auto;
    }

    /* =====================================================
       PAGE HEADING
    ===================================================== */

    .categories-heading {
        text-align: center;
        margin-bottom: 45px;
    }

    .categories-heading .section-label {
        display: inline-block;
        margin-bottom: 10px;
        color: var(--primary, #b85c70);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    .categories-heading h1 {
        margin: 0;
        color: var(--text, #292624);
        font-size: 42px;
        line-height: 1.2;
    }

    .categories-heading p {
        max-width: 650px;
        margin: 14px auto 0;
        color: var(--text-light, #706b68);
        font-size: 16px;
        line-height: 1.7;
    }

    /* =====================================================
       CATEGORY GRID
    ===================================================== */

    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .category-card {
        display: block;
        overflow: hidden;
        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 18px;
        text-decoration: none;
        box-shadow: 0 8px 25px rgba(70, 45, 50, 0.06);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    .category-card:hover {
        transform: translateY(-6px);
        border-color: #e5b8c3;
        box-shadow: 0 16px 35px rgba(70, 45, 50, 0.12);
    }

    /* =====================================================
       CATEGORY IMAGE
    ===================================================== */

    .category-image {
        position: relative;
        width: 100%;
        height: 240px;
        overflow: hidden;
        background: var(--primary-light, #f8e7eb);
    }

    .category-image img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .category-card:hover .category-image img {
        transform: scale(1.06);
    }

    .category-image-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to bottom,
            rgba(0, 0, 0, 0.02),
            rgba(0, 0, 0, 0.30)
        );
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .category-card:hover .category-image-overlay {
        opacity: 1;
    }

    /* =====================================================
       ARROW
    ===================================================== */

    .category-arrow {
        position: absolute;
        right: 15px;
        bottom: 15px;
        width: 42px;
        height: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: var(--white, #ffffff);
        color: var(--primary, #b85c70);

        border-radius: 50%;
        font-size: 21px;
        font-weight: 700;

        opacity: 0;
        transform: translateY(8px);

        transition:
            opacity 0.25s ease,
            transform 0.25s ease;
    }

    .category-card:hover .category-arrow {
        opacity: 1;
        transform: translateY(0);
    }

    /* =====================================================
       CATEGORY NAME
    ===================================================== */

    .category-card h3 {
        margin: 0;
        padding: 18px 20px 21px;

        color: var(--text, #292624);
        font-size: 18px;
        font-weight: 700;
        text-align: center;

        transition: color 0.2s ease;
    }

    .category-card:hover h3 {
        color: var(--primary, #b85c70);
    }

    /* =====================================================
       EMPTY MESSAGE
    ===================================================== */

    .empty-message {
        grid-column: 1 / -1;

        padding: 50px 25px;
        text-align: center;

        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 18px;

        color: var(--text-light, #706b68);
        font-size: 15px;
    }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1000px) {
        .category-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 750px) {
        .categories-page {
            padding: 50px 16px 60px;
        }

        .categories-heading h1 {
            font-size: 34px;
        }

        .category-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .category-image {
            height: 210px;
        }
    }

    @media (max-width: 500px) {
        .categories-heading {
            margin-bottom: 30px;
        }

        .categories-heading h1 {
            font-size: 30px;
        }

        .categories-heading p {
            font-size: 14px;
        }

        .category-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .category-image {
            height: 230px;
        }

        .category-arrow {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<main class="categories-page">

    <div class="categories-container">

        <!-- =================================================
             PAGE HEADING
        ================================================== -->

        <div class="categories-heading">

            <span class="section-label">
                OUR COLLECTION
            </span>

            <h1>
                Shop by Flower Type
            </h1>

            <p>
                Explore our beautiful collection of fresh flowers
                and find the perfect blooms for every special moment.
            </p>

        </div>


        <!-- =================================================
             CATEGORY GRID
        ================================================== -->

        <div class="category-grid">

            <?php if ($categoryResult && $categoryResult->num_rows > 0): ?>

                <?php while ($category = $categoryResult->fetch_assoc()): ?>

                    <?php

                    $categoryImage = !empty($category["image"])
                        ? "images/" . $category["image"]
                        : "images/default-flower.jpg";

                    ?>

                    <a
                        href="products.php?category=<?= (int)$category["id"] ?>"
                        class="category-card"
                    >

                        <div class="category-image">

                            <img
                                src="<?= htmlspecialchars($categoryImage) ?>"
                                alt="<?= htmlspecialchars($category["category_name"]) ?>"
                                onerror="this.src='images/default-flower.jpg';"
                            >

                            <div class="category-image-overlay"></div>

                            <div class="category-arrow">
                                →
                            </div>

                        </div>

                        <h3>
                            <?= htmlspecialchars($category["category_name"]) ?>
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

</main>


<?php require_once "includes/footer.php"; ?>