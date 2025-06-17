<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get data for homepage
$sliders = getSliders();
$categories = getCategories(8); // Get 8 categories for slider
$recentJobs = getJobs(['limit' => 10, 'orderBy' => 'jobId DESC']);
$featuredSeekers = getSeekers(['limit' => 6, 'filters' => ['isFeatured' => 1]]);
$recentArticles = getArticles(['limit' => 4, 'orderBy' => 'articleId DESC']);
$configs = getConfigs();
?>

<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>" dir="<?php echo getCurrentLanguage() == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('home_title'); ?> - Wazfnee</title>
    <meta name="description" content="<?php echo t('home_description'); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Slider -->
    <section class="hero-slider">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($sliders as $index => $slider): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo $slider['image']; ?>" class="d-block w-100" alt="Slider">
                    <div class="carousel-caption">
                        <div class="container">
                            <h1 class="display-4 fw-bold"><?php echo t('find_your_dream_job'); ?></h1>
                            <p class="lead"><?php echo t('hero_subtitle'); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="search-card bg-white p-4 rounded-3 shadow">
                        <form action="jobs.php" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" placeholder="<?php echo t('search_jobs'); ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="category">
                                    <option value=""><?php echo t('all_categories'); ?></option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['categoryId']; ?>">
                                        <?php echo getCurrentLanguage() == 'ar' ? $category['nameAr'] : $category['nameEn']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="country">
                                    <option value=""><?php echo t('all_countries'); ?></option>
                                    <!-- Countries will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> <?php echo t('search'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Slider -->
    <section class="categories-section py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5"><?php echo t('browse_categories'); ?></h2>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <a href="jobs.php?category=<?php echo $category['categoryId']; ?>" class="category-card text-decoration-none">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body">
                                <img src="<?php echo $category['image']; ?>" alt="Category" class="category-icon mb-3">
                                <h5 class="card-title">
                                    <?php echo getCurrentLanguage() == 'ar' ? $category['nameAr'] : $category['nameEn']; ?>
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-content py-5">
        <div class="container">
            <div class="row">
                <!-- Recent Jobs -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3><?php echo t('recent_jobs'); ?></h3>
                        <a href="jobs.php" class="btn btn-outline-primary"><?php echo t('view_all'); ?></a>
                    </div>
                    
                    <div class="jobs-list">
                        <?php foreach ($recentJobs as $job): ?>
                        <div class="job-card card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="job-title mb-2">
                                            <a href="job-details.php?id=<?php echo $job['jobId']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h5>
                                        <div class="job-meta text-muted mb-2">
                                            <span><i class="fas fa-building"></i> <?php echo $job['categoryName']; ?></span>
                                            <span class="ms-3"><i class="fas fa-map-marker-alt"></i> <?php echo $job['countryName']; ?></span>
                                            <?php if (!$job['isHideSalary']): ?>
                                            <span class="ms-3"><i class="fas fa-money-bill"></i> <?php echo $job['monthlySalary'] . ' ' . $job['currencyName']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="job-description text-muted mb-0">
                                            <?php echo truncateText($job['details'], 150); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="job-actions">
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-eye"></i> <?php echo $job['views']; ?> <?php echo t('views'); ?>
                                            </small>
                                            <small class="text-muted d-block">
                                                <?php echo timeAgo($job['createdAt']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Post Job Section -->
                    <div class="card mb-4 border-0 shadow-sm bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-plus-circle fa-3x mb-3"></i>
                            <h4><?php echo t('post_job'); ?></h4>
                            <p><?php echo t('post_job_description'); ?></p>
                            <a href="<?php echo isLoggedIn() ? 'post-job.php' : 'login.php'; ?>" class="btn btn-light btn-lg">
                                <?php echo t('post_now'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Featured Job Seekers -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><?php echo t('featured_seekers'); ?></h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($featuredSeekers as $seeker): ?>
                            <div class="seeker-item d-flex align-items-center mb-3 pb-3 border-bottom">
                                <img src="<?php echo $seeker['userImage'] ?: 'assets/images/default-avatar.png'; ?>" 
                                     alt="Profile" class="rounded-circle me-3" width="50" height="50">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="seeker-profile.php?id=<?php echo $seeker['seekerId']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($seeker['userName']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo $seeker['categoryName']; ?> • <?php echo $seeker['yearsOfExperience']; ?> <?php echo t('years_exp'); ?>
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <a href="seekers.php" class="btn btn-outline-primary btn-sm w-100"><?php echo t('view_all_seekers'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Blog Posts -->
    <section class="blog-section py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h3><?php echo t('latest_articles'); ?></h3>
                <a href="blog.php" class="btn btn-outline-primary"><?php echo t('view_all'); ?></a>
            </div>
            
            <div class="row">
                <?php foreach ($recentArticles as $article): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="<?php echo $article['image']; ?>" class="card-img-top" alt="Article">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="article.php?id=<?php echo $article['articleId']; ?>" class="text-decoration-none">
                                    <?php echo getCurrentLanguage() == 'ar' ? $article['titleAr'] : $article['titleEn']; ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($article['createdAt'])); ?>
                                <span class="ms-2"><i class="fas fa-eye"></i> <?php echo $article['views']; ?></span>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- App Download Section -->
    <section class="app-download py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3><?php echo t('download_app'); ?></h3>
                    <p class="lead"><?php echo t('app_description'); ?></p>
                    <div class="app-buttons">
                        <a href="#" class="btn btn-dark btn-lg me-3 mb-3">
                            <i class="fab fa-apple"></i> App Store
                        </a>
                        <a href="#" class="btn btn-dark btn-lg mb-3">
                            <i class="fab fa-google-play"></i> Google Play
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="assets/images/app-mockup.png" alt="Mobile App" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info -->
    <section class="contact-info py-5 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-map-marker-alt"></i> <?php echo t('address'); ?></h5>
                    <p><?php echo getConfig('address'); ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-phone"></i> <?php echo t('phone'); ?></h5>
                    <p><?php echo getConfig('phone'); ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-envelope"></i> <?php echo t('email'); ?></h5>
                    <p><?php echo getConfig('email'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>