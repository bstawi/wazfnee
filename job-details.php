<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$jobId = intval($_GET['id'] ?? 0);

if (!$jobId) {
    header('Location: jobs.php');
    exit;
}

// Get job details
$job = getJob($jobId);

if (!$job) {
    header('Location: jobs.php');
    exit;
}

// Increment view count
makeApiRequest('increment_job_views.php', 'POST', ['jobId' => $jobId]);

// Get related jobs
$relatedJobs = getJobs([
    'filtersMap' => json_encode(['categoryId' => $job['categoryId']]),
    'limit' => 4,
    'orderBy' => 'RAND()'
]);

$pageTitle = $job['title'];
$pageDescription = truncateText($job['details'], 160);
?>

<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>" dir="<?php echo getCurrentLanguage() == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php generateMetaTags(
        $pageTitle,
        $pageDescription,
        $job['categoryName'] . ', jobs, careers, employment',
        $job['image'] ?? ''
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
            <div class="col-lg-8">
                <!-- Job Header -->
                <div class="job-header mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h2 mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                            <div class="job-meta text-muted">
                                <span><i class="fas fa-building"></i> <?php echo $job['categoryName']; ?></span>
                                <span class="ms-3"><i class="fas fa-map-marker-alt"></i> <?php echo $job['countryName']; ?></span>
                                <span class="ms-3"><i class="fas fa-eye"></i> <?php echo $job['views']; ?> <?php echo t('views'); ?></span>
                                <span class="ms-3"><i class="fas fa-clock"></i> <?php echo timeAgo($job['createdAt']); ?></span>
                            </div>
                        </div>
                        <div class="job-status">
                            <?php if ($job['adStatus'] === 'active'): ?>
                            <span class="badge bg-success fs-6"><?php echo t('active'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Job Info Cards -->
                    <div class="row mb-4">
                        <?php if (!$job['isHideSalary'] && $job['monthlySalary']): ?>
                        <div class="col-md-3 mb-3">
                            <div class="info-card text-center p-3 border rounded">
                                <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
                                <h6><?php echo t('salary'); ?></h6>
                                <p class="mb-0 fw-bold"><?php echo formatSalary($job['monthlySalary'], $job['currencyName']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3 mb-3">
                            <div class="info-card text-center p-3 border rounded">
                                <i class="fas fa-tags text-primary fa-2x mb-2"></i>
                                <h6><?php echo t('category'); ?></h6>
                                <p class="mb-0"><?php echo $job['categoryName']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-card text-center p-3 border rounded">
                                <i class="fas fa-globe text-info fa-2x mb-2"></i>
                                <h6><?php echo t('location'); ?></h6>
                                <p class="mb-0"><?php echo $job['countryName']; ?></p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="info-card text-center p-3 border rounded">
                                <i class="fas fa-eye text-warning fa-2x mb-2"></i>
                                <h6><?php echo t('views'); ?></h6>
                                <p class="mb-0"><?php echo number_format($job['views']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="job-description mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h4 class="mb-0"><?php echo t('job_description'); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="job-content">
                                <?php echo nl2br(htmlspecialchars($job['details'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Share Section -->
                <div class="share-section mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="mb-3"><?php echo t('share_job'); ?></h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary btn-sm" onclick="shareOnFacebook('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>', '<?php echo addslashes($job['title']); ?>')">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </button>
                                <button class="btn btn-info btn-sm" onclick="shareOnTwitter('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>', '<?php echo addslashes($job['title']); ?>')">
                                    <i class="fab fa-twitter"></i> Twitter
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="shareOnLinkedIn('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>', '<?php echo addslashes($job['title']); ?>')">
                                    <i class="fab fa-linkedin-in"></i> LinkedIn
                                </button>
                                <button class="btn btn-success btn-sm" onclick="shareOnWhatsApp('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>', '<?php echo addslashes($job['title']); ?>')">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="copyToClipboard('<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>')">
                                    <i class="fas fa-copy"></i> <?php echo t('copy_link'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Employer Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo t('employer_info'); ?></h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?php echo $job['userImage'] ?: 'assets/images/default-company.png'; ?>" 
                             alt="Employer" class="rounded-circle mb-3" width="80" height="80">
                        <h6 class="mb-2"><?php echo htmlspecialchars($job['userName']); ?></h6>
                        <p class="text-muted small mb-3"><?php echo t('employer'); ?></p>
                        
                        <?php if ($job['userBio']): ?>
                        <p class="small text-muted"><?php echo truncateText($job['userBio'], 100); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Application Notice -->
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo t('before_applying'); ?>
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li><?php echo t('read_job_description'); ?></li>
                            <li><?php echo t('prepare_cv'); ?></li>
                            <li><?php echo t('be_professional'); ?></li>
                            <li><?php echo t('follow_instructions'); ?></li>
                        </ul>
                    </div>
                </div>

                <!-- Apply Buttons -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-center">
                            <i class="fas fa-paper-plane"></i> <?php echo t('apply_now'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if ($job['whatsAppNumber']): ?>
                            <a href="https://wa.me/<?php echo $job['dialingCode'] . $job['whatsAppNumber']; ?>?text=<?php echo urlencode(t('interested_in_job') . ': ' . $job['title']); ?>" 
                               class="btn btn-success btn-lg" target="_blank">
                                <i class="fab fa-whatsapp"></i> <?php echo t('apply_whatsapp'); ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($job['emailAddress']): ?>
                            <a href="mailto:<?php echo $job['emailAddress']; ?>?subject=<?php echo urlencode(t('application_for') . ': ' . $job['title']); ?>&body=<?php echo urlencode(t('email_template')); ?>" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-envelope"></i> <?php echo t('apply_email'); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> <?php echo t('safe_application'); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Related Jobs -->
                <?php if (!empty($relatedJobs)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><?php echo t('related_jobs'); ?></h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($relatedJobs as $relatedJob): ?>
                        <?php if ($relatedJob['jobId'] != $job['jobId']): ?>
                        <div class="related-job-item mb-3 pb-3 border-bottom">
                            <h6 class="mb-1">
                                <a href="job-details.php?id=<?php echo $relatedJob['jobId']; ?>" class="text-decoration-none">
                                    <?php echo truncateText($relatedJob['title'], 60); ?>
                                </a>
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-building"></i> <?php echo $relatedJob['categoryName']; ?>
                                <span class="ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $relatedJob['countryName']; ?></span>
                            </small>
                            <?php if (!$relatedJob['isHideSalary'] && $relatedJob['monthlySalary']): ?>
                            <div class="small text-success mt-1">
                                <i class="fas fa-money-bill"></i> <?php echo formatSalary($relatedJob['monthlySalary'], $relatedJob['currencyName']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <a href="jobs.php?category=<?php echo $job['categoryId']; ?>" class="btn btn-outline-primary btn-sm w-100">
                            <?php echo t('view_more_jobs'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "JobPosting",
        "title": "<?php echo addslashes($job['title']); ?>",
        "description": "<?php echo addslashes($pageDescription); ?>",
        "datePosted": "<?php echo date('c', strtotime($job['createdAt'])); ?>",
        "employmentType": "FULL_TIME",
        "hiringOrganization": {
            "@type": "Organization",
            "name": "<?php echo addslashes($job['userName']); ?>"
        },
        "jobLocation": {
            "@type": "Place",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "<?php echo $job['countryName']; ?>"
            }
        },
        <?php if (!$job['isHideSalary'] && $job['monthlySalary']): ?>
        "baseSalary": {
            "@type": "MonetaryAmount",
            "currency": "<?php echo $job['currencyName']; ?>",
            "value": {
                "@type": "QuantitativeValue",
                "value": <?php echo $job['monthlySalary']; ?>,
                "unitText": "MONTH"
            }
        },
        <?php endif; ?>
        "industry": "<?php echo $job['categoryName']; ?>"
    }
    </script>
</body>
</html>
```