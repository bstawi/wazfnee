<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Get admin statistics
$statsParams = [
    'startTimeToday' => date('Y-m-d 00:00:00'),
    'endTimeToday' => date('Y-m-d 23:59:59'),
    'startThisMonth' => date('Y-m-01 00:00:00'),
    'endThisMonth' => date('Y-m-t 23:59:59'),
    'startLastMonth' => date('Y-m-01 00:00:00', strtotime('last month')),
    'endLastMonth' => date('Y-m-t 23:59:59', strtotime('last month'))
];

$response = makeApiRequest('get_admin_statistics.php?' . http_build_query($statsParams));
$statistics = $response['adminStatistics'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Wazfnee</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include 'includes/header.php'; ?>
        
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Dashboard</h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshStats()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <?php foreach ($statistics as $stat): ?>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1"><?php echo $stat['titleEn']; ?></h6>
                                    <h3 class="mb-0"><?php echo number_format($stat['count']); ?></h3>
                                </div>
                                <div class="stat-icon">
                                    <?php
                                    $icons = [
                                        'admins' => 'fas fa-user-shield',
                                        'users' => 'fas fa-users',
                                        'categories' => 'fas fa-tags',
                                        'sliders' => 'fas fa-images',
                                        'articles' => 'fas fa-newspaper',
                                        'complaints' => 'fas fa-exclamation-triangle',
                                        'activeJobs' => 'fas fa-briefcase text-success',
                                        'pendingJobs' => 'fas fa-clock text-warning',
                                        'rejectedJobs' => 'fas fa-times-circle text-danger',
                                        'activeSeekers' => 'fas fa-user-tie text-success',
                                        'pendingSeekers' => 'fas fa-user-clock text-warning',
                                        'rejectedSeekers' => 'fas fa-user-times text-danger',
                                        'jobs' => 'fas fa-briefcase',
                                        'jobSeekers' => 'fas fa-user-tie'
                                    ];
                                    $iconClass = $icons[$stat['label']] ?? 'fas fa-chart-bar';
                                    ?>
                                    <i class="<?php echo $iconClass; ?> fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="jobs.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-briefcase"></i><br>
                                        Manage Jobs
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="seekers.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-user-tie"></i><br>
                                        Manage Seekers
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="users.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-users"></i><br>
                                        Manage Users
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="articles.php" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-newspaper"></i><br>
                                        Manage Articles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Recent Jobs</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentJobsTable">
                                        <!-- Will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Recent Notifications</h5>
                        </div>
                        <div class="card-body">
                            <div id="recentNotifications">
                                <!-- Will be loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script>
        // Load recent data
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentJobs();
            loadRecentNotifications();
        });

        function refreshStats() {
            location.reload();
        }

        function loadRecentJobs() {
            fetch('../api/get_jobs.php?limit=5&orderBy=jobId DESC')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('recentJobsTable');
                    if (data.jobs && data.jobs.length > 0) {
                        tbody.innerHTML = data.jobs.map(job => `
                            <tr>
                                <td>
                                    <a href="job-details.php?id=${job.jobId}" class="text-decoration-none">
                                        ${job.title}
                                    </a>
                                </td>
                                <td>${job.categoryName || 'N/A'}</td>
                                <td>
                                    <span class="badge bg-${job.adStatus === 'active' ? 'success' : job.adStatus === 'pending' ? 'warning' : 'danger'}">
                                        ${job.adStatus}
                                    </span>
                                </td>
                                <td>${new Date(job.createdAt).toLocaleDateString()}</td>
                                <td>
                                    <a href="jobs.php?id=${job.jobId}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent jobs</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading recent jobs:', error);
                });
        }

        function loadRecentNotifications() {
            fetch('../api/get_admin_notifications.php?limit=5&orderBy=adminNotificationId DESC')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentNotifications');
                    if (data.adminNotifications && data.adminNotifications.length > 0) {
                        container.innerHTML = data.adminNotifications.map(notification => `
                            <div class="notification-item mb-3 p-3 border rounded ${notification.isViewed ? '' : 'bg-light'}">
                                <h6 class="mb-1">${notification.title}</h6>
                                <p class="mb-1 small text-muted">${notification.body}</p>
                                <small class="text-muted">${new Date(notification.createdAt).toLocaleDateString()}</small>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-muted text-center">No recent notifications</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }
    </script>
</body>
</html>