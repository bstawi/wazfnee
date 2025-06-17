<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png" alt="Wazfnee" height="40">
            </a>

            <!-- Mobile toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><?php echo t('home'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php"><?php echo t('jobs'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="seekers.php"><?php echo t('seekers'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php"><?php echo t('blog'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php"><?php echo t('contact'); ?></a>
                    </li>
                </ul>

                <!-- Right side navigation -->
                <ul class="navbar-nav">
                    <!-- Language Toggle -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo getCurrentLanguage() == 'ar' ? 'العربية' : 'English'; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?lang=ar">العربية</a></li>
                            <li><a class="dropdown-item" href="?lang=en">English</a></li>
                        </ul>
                    </li>

                    <?php if (isLoggedIn()): ?>
                        <!-- User Menu -->
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white me-2" href="post-job.php">
                                <i class="fas fa-plus"></i> <?php echo t('post_job'); ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> <?php echo t('profile'); ?></a></li>
                                <li><a class="dropdown-item" href="my-jobs.php"><i class="fas fa-briefcase"></i> <?php echo t('my_jobs'); ?></a></li>
                                <li><a class="dropdown-item" href="cv.php"><i class="fas fa-file-alt"></i> <?php echo t('cv'); ?></a></li>
                                <li><a class="dropdown-item" href="favorites.php"><i class="fas fa-heart"></i> <?php echo t('favorites'); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key"></i> <?php echo t('change_password'); ?></a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> <?php echo t('logout'); ?></a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest Menu -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><?php echo t('login'); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white" href="register.php"><?php echo t('register'); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>