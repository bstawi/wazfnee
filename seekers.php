<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get search parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$country = $_GET['country'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));

// Build filters
$filters = [];
if ($category) $filters['categoryId'] = $category;
if ($country) $filters['country'] = $country;

// Get seekers
$params = [
    'searchText' => $search,
    'filtersMap' => json_encode($filters),
    'page' => $page,
    'limit' => ITEMS_PER_PAGE,
    'isPaginated' => true
];

$response = makeApiRequest('get_seekers.php?' . http_build_query($params));
$seekers = $response['seekers'] ?? [];
$pagination = $response['pagination'] ?? [];

// Get categories for filter
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>" dir="<?php echo getCurrentLanguage() == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php generateMetaTags(
        t('seekers') . ($search ? ' - ' . $search : ''),
        'Browse profiles of talented job seekers looking for opportunities',
        'job seekers, candidates, professionals, talent'
    ); ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1><?php echo t('job_seekers'); ?></h1>
                        <?php if ($search): ?>
                        <p class="text-muted"><?php echo t('search_results_for'); ?>: "<?php echo htmlspecialchars($search); ?>"</p>
                        <?php endif; ?>
                        <?php if (isset($pagination['total'])): ?>
                        <small class="text-muted">
                            <?php echo number_format($pagination['total']); ?> <?php echo t('seekers_found'); ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="sortBy" onchange="updateSort(this.value)">
                            <option value="seekerId DESC"><?php echo t('newest_first'); ?></option>
                            <option value="seekerId ASC"><?php echo t('oldest_first'); ?></option>
                            <option value="views DESC"><?php echo t('most_viewed'); ?></option>
                            <option value="yearsOfExperience DESC"><?php echo t('most_experienced'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Seekers Grid -->
                <div class="seekers-grid">
                    <?php if (empty($seekers)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                        <h4><?php echo t('no_seekers_found'); ?></h4>
                        <p class="text-muted"><?php echo t('try_different_search'); ?></p>
                        <a href="seekers.php" class="btn btn-primary"><?php echo t('view_all_seekers'); ?></a>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($seekers as $seeker): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="seeker-card card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <!-- Profile Image -->
                                    <div class="position-relative mb-3">
                                        <img src="<?php echo $seeker['userImage'] ?: 'assets/images/default-avatar.png'; ?>" 
                                             alt="Profile" class="rounded-circle mb-2" width="80" height="80">
                                        <?php if ($seeker['isFeatured']): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Name and Verification -->
                                    <h5 class="card-title mb-1">
                                        <a href="seeker-profile.php?id=<?php echo $seeker['seekerId']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($seeker['userName']); ?>
                                        </a>
                                        <?php if ($seeker['isVerified']): ?>
                                        <i class="fas fa-check-circle text-primary ms-1" title="<?php echo t('verified'); ?>"></i>
                                        <?php endif; ?>
                                    </h5>

                                    <!-- Professional Info -->
                                    <div class="seeker-info mb-3">
                                        <div class="info-item mb-2">
                                            <i class="fas fa-briefcase text-primary"></i>
                                            <span class="ms-2"><?php echo $seeker['categoryName']; ?></span>
                                        </div>
                                        <div class="info-item mb-2">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            <span class="ms-2"><?php echo $seeker['countryName']; ?></span>
                                        </div>
                                        <div class="info-item mb-2">
                                            <i class="fas fa-user text-info"></i>
                                            <span class="ms-2"><?php echo $seeker['gender'] == 'male' ? t('male') : t('female'); ?></span>
                                        </div>
                                        <div class="info-item mb-2">
                                            <i class="fas fa-calendar text-warning"></i>
                                            <span class="ms-2"><?php echo $seeker['age']; ?> <?php echo t('years_old'); ?></span>
                                        </div>
                                        <div class="info-item mb-2">
                                            <i class="fas fa-chart-line text-success"></i>
                                            <span class="ms-2"><?php echo $seeker['yearsOfExperience']; ?> <?php echo t('years_exp'); ?></span>
                                        </div>
                                    </div>

                                    <!-- Views Count -->
                                    <div class="views-count mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> <?php echo $seeker['views']; ?> <?php echo t('profile_views'); ?>
                                        </small>
                                    </div>

                                    <!-- Brief About -->
                                    <?php if ($seeker['briefAboutMe']): ?>
                                    <p class="card-text small text-muted mb-3">
                                        <?php echo truncateText($seeker['briefAboutMe'], 100); ?>
                                    </p>
                                    <?php endif; ?>

                                    <!-- Education -->
                                    <?php if ($seeker['universityDegree']): ?>
                                    <div class="education mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($seeker['universityDegree']); ?>
                                        </small>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Contact Actions -->
                                    <div class="contact-actions">
                                        <div class="btn-group w-100" role="group">
                                            <?php if ($seeker['phoneNumber']): ?>
                                            <a href="tel:<?php echo $seeker['dialingCode'] . $seeker['phoneNumber']; ?>" 
                                               class="btn btn-outline-primary btn-sm" title="<?php echo t('call'); ?>">
                                                <i class="fas fa-phone"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($seeker['whatsAppNumber']): ?>
                                            <a href="https://wa.me/<?php echo $seeker['dialingCode'] . $seeker['whatsAppNumber']; ?>" 
                                               class="btn btn-outline-success btn-sm" target="_blank" title="WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($seeker['emailAddress']): ?>
                                            <a href="mailto:<?php echo $seeker['emailAddress']; ?>" 
                                               class="btn btn-outline-info btn-sm" title="<?php echo t('email'); ?>">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                            <?php endif; ?>
                                            
                                            <a href="seeker-profile.php?id=<?php echo $seeker['seekerId']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <?php echo t('view_profile'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Status and Date -->
                                    <div class="seeker-footer mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo timeAgo($seeker['createdAt']); ?>
                                            </small>
                                            <?php if ($seeker['adStatus'] === 'active'): ?>
                                            <span class="badge bg-success"><?php echo t('available'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination['totalPages']) && $pagination['totalPages'] > 1): ?>
                <nav aria-label="Seekers pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['hasPrev']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['page'] - 1])); ?>">
                                <i class="fas fa-chevron-left"></i> <?php echo t('previous'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $pagination['page'] - 2);
                        $end = min($pagination['totalPages'], $pagination['page'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                        <li class="page-item <?php echo $i == $pagination['page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['hasNext']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $pagination['page'] + 1])); ?>">
                                <?php echo t('next'); ?> <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <!-- Search Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo t('filter_seekers'); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="seekers.php">
                            <div class="mb-3">
                                <label class="form-label"><?php echo t('search'); ?></label>
                                <input type="text" class="form-control" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="<?php echo t('search_seekers'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?php echo t('category'); ?></label>
                                <select class="form-select" name="category">
                                    <option value=""><?php echo t('all_categories'); ?></option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['categoryId']; ?>" 
                                            <?php echo $category == $cat['categoryId'] ? 'selected' : ''; ?>>
                                        <?php echo getCurrentLanguage() == 'ar' ? $cat['nameAr'] : $cat['nameEn']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><?php echo t('country'); ?></label>
                                <select class="form-select" name="country">
                                    <option value=""><?php echo t('all_countries'); ?></option>
                                    <!-- Countries will be loaded via JavaScript -->
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> <?php echo t('apply_filters'); ?>
                            </button>
                            
                            <?php if ($search || $category || $country): ?>
                            <a href="seekers.php" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-times"></i> <?php echo t('clear_filters'); ?>
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Post CV CTA -->
                <div class="card mb-4 bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-2x mb-3"></i>
                        <h5><?php echo t('post_cv'); ?></h5>
                        <p class="small"><?php echo t('post_cv_description'); ?></p>
                        <a href="<?php echo isLoggedIn() ? 'post-cv.php' : 'login.php'; ?>" 
                           class="btn btn-light">
                            <?php echo t('post_now'); ?>
                        </a>
                    </div>
                </div>

                <!-- Featured Seekers -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><?php echo t('featured_seekers'); ?></h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $featuredSeekers = getSeekers(['limit' => 5, 'filters' => ['isFeatured' => 1]]);
                        foreach ($featuredSeekers as $featured):
                        ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="<?php echo $featured['userImage'] ?: 'assets/images/default-avatar.png'; ?>" 
                                 alt="Profile" class="rounded-circle me-3" width="40" height="40">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">
                                    <a href="seeker-profile.php?id=<?php echo $featured['seekerId']; ?>" 
                                       class="text-decoration-none">
                                        <?php echo htmlspecialchars($featured['userName']); ?>
                                    </a>
                                    <i class="fas fa-star text-warning ms-1"></i>
                                </h6>
                                <small class="text-muted">
                                    <?php echo $featured['categoryName']; ?> • <?php echo $featured['yearsOfExperience']; ?> <?php echo t('years_exp'); ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        function updateSort(sortBy) {
            const url = new URL(window.location);
            url.searchParams.set('orderBy', sortBy);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        // Set current sort value
        const urlParams = new URLSearchParams(window.location.search);
        const currentSort = urlParams.get('orderBy') || 'seekerId DESC';
        document.getElementById('sortBy').value = currentSort;
    </script>
</body>
</html>
```