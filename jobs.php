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

// Get jobs
$params = [
    'searchText' => $search,
    'filtersMap' => json_encode($filters),
    'page' => $page,
    'limit' => ITEMS_PER_PAGE,
    'isPaginated' => true
];

$response = makeApiRequest('get_jobs.php?' . http_build_query($params));
$jobs = $response['jobs'] ?? [];
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
        t('jobs') . ($search ? ' - ' . $search : ''),
        'Browse thousands of job opportunities in various categories and locations',
        'jobs, careers, employment, work, hiring'
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
                        <h1><?php echo t('jobs'); ?></h1>
                        <?php if ($search): ?>
                        <p class="text-muted"><?php echo t('search_results_for'); ?>: "<?php echo htmlspecialchars($search); ?>"</p>
                        <?php endif; ?>
                        <?php if (isset($pagination['total'])): ?>
                        <small class="text-muted">
                            <?php echo number_format($pagination['total']); ?> <?php echo t('jobs_found'); ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="sortBy" onchange="updateSort(this.value)">
                            <option value="jobId DESC"><?php echo t('newest_first'); ?></option>
                            <option value="jobId ASC"><?php echo t('oldest_first'); ?></option>
                            <option value="views DESC"><?php echo t('most_viewed'); ?></option>
                            <option value="title ASC"><?php echo t('alphabetical'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="jobs-list">
                    <?php if (empty($jobs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4><?php echo t('no_jobs_found'); ?></h4>
                        <p class="text-muted"><?php echo t('try_different_search'); ?></p>
                        <a href="jobs.php" class="btn btn-primary"><?php echo t('view_all_jobs'); ?></a>
                    </div>
                    <?php else: ?>
                        <?php foreach ($jobs as $job): ?>
                        <div class="job-card card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="job-title mb-0">
                                                <a href="job-details.php?id=<?php echo $job['jobId']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($job['title']); ?>
                                                </a>
                                            </h5>
                                            <?php if ($job['adStatus'] === 'active'): ?>
                                            <span class="badge bg-success"><?php echo t('active'); ?></span>
                                            <?php elseif ($job['adStatus'] === 'pending'): ?>
                                            <span class="badge bg-warning"><?php echo t('pending'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="job-meta text-muted mb-3">
                                            <span><i class="fas fa-building"></i> <?php echo $job['categoryName']; ?></span>
                                            <span class="ms-3"><i class="fas fa-map-marker-alt"></i> <?php echo $job['countryName']; ?></span>
                                            <?php if (!$job['isHideSalary'] && $job['monthlySalary']): ?>
                                            <span class="ms-3">
                                                <i class="fas fa-money-bill"></i> 
                                                <?php echo formatSalary($job['monthlySalary'], $job['currencyName']); ?>
                                            </span>
                                            <?php endif; ?>
                                            <span class="ms-3"><i class="fas fa-eye"></i> <?php echo $job['views']; ?> <?php echo t('views'); ?></span>
                                        </div>
                                        
                                        <p class="job-description text-muted mb-3">
                                            <?php echo truncateText($job['details'], 200); ?>
                                        </p>
                                        
                                        <div class="job-footer d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> <?php echo timeAgo($job['createdAt']); ?>
                                            </small>
                                            <div class="job-actions">
                                                <?php if ($job['whatsAppNumber']): ?>
                                                <a href="https://wa.me/<?php echo $job['dialingCode'] . $job['whatsAppNumber']; ?>" 
                                                   class="btn btn-success btn-sm me-2" target="_blank">
                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                </a>
                                                <?php endif; ?>
                                                <?php if ($job['emailAddress']): ?>
                                                <a href="mailto:<?php echo $job['emailAddress']; ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-envelope"></i> Email
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 text-center">
                                        <div class="employer-info">
                                            <img src="<?php echo $job['userImage'] ?: 'assets/images/default-company.png'; ?>" 
                                                 alt="Company" class="rounded-circle mb-2" width="60" height="60">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($job['userName']); ?></h6>
                                            <small class="text-muted"><?php echo t('employer'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination['totalPages']) && $pagination['totalPages'] > 1): ?>
                <nav aria-label="Jobs pagination">
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
                        <h5 class="mb-0"><?php echo t('filter_jobs'); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="jobs.php">
                            <div class="mb-3">
                                <label class="form-label"><?php echo t('search'); ?></label>
                                <input type="text" class="form-control" name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="<?php echo t('search_jobs'); ?>">
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
                            <a href="jobs.php" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-times"></i> <?php echo t('clear_filters'); ?>
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Post Job CTA -->
                <div class="card mb-4 bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-plus-circle fa-2x mb-3"></i>
                        <h5><?php echo t('post_job'); ?></h5>
                        <p class="small"><?php echo t('post_job_description'); ?></p>
                        <a href="<?php echo isLoggedIn() ? 'post-job.php' : 'login.php'; ?>" 
                           class="btn btn-light">
                            <?php echo t('post_now'); ?>
                        </a>
                    </div>
                </div>

                <!-- Recent Jobs -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><?php echo t('recent_jobs'); ?></h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $recentJobs = getJobs(['limit' => 5, 'orderBy' => 'jobId DESC']);
                        foreach ($recentJobs as $recentJob):
                        ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="<?php echo $recentJob['userImage'] ?: 'assets/images/default-company.png'; ?>" 
                                 alt="Company" class="rounded me-3" width="40" height="40">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">
                                    <a href="job-details.php?id=<?php echo $recentJob['jobId']; ?>" 
                                       class="text-decoration-none">
                                        <?php echo truncateText($recentJob['title'], 50); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <?php echo $recentJob['categoryName']; ?>
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
        const currentSort = urlParams.get('orderBy') || 'jobId DESC';
        document.getElementById('sortBy').value = currentSort;
    </script>
</body>
</html>