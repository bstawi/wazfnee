<footer class="footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Logo Section -->
            <div class="col-lg-4 mb-4">
                <img src="assets/images/logo-white.png" alt="Wazfnee" height="50" class="mb-3">
                <p class="text-muted">
                    <?php echo getCurrentLanguage() == 'ar' ? 
                        'وظفني هو أفضل موقع للبحث عن الوظائف في المنطقة العربية' : 
                        'Wazfnee is the best job search website in the Arab region'; ?>
                </p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Help Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3"><?php echo getCurrentLanguage() == 'ar' ? 'المساعدة' : 'Help'; ?></h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="faq.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'الأسئلة الشائعة' : 'FAQ'; ?>
                    </a></li>
                    <li class="mb-2"><a href="support.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'الدعم الفني' : 'Support'; ?>
                    </a></li>
                    <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'اتصل بنا' : 'Contact Us'; ?>
                    </a></li>
                    <li class="mb-2"><a href="feedback.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'ملاحظات' : 'Feedback'; ?>
                    </a></li>
                </ul>
            </div>

            <!-- Important Links -->
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3"><?php echo getCurrentLanguage() == 'ar' ? 'روابط مهمة' : 'Important Links'; ?></h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'من نحن' : 'About Us'; ?>
                    </a></li>
                    <li class="mb-2"><a href="privacy.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy'; ?>
                    </a></li>
                    <li class="mb-2"><a href="terms.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'شروط الاستخدام' : 'Terms of Service'; ?>
                    </a></li>
                    <li class="mb-2"><a href="careers.php" class="text-muted text-decoration-none">
                        <?php echo getCurrentLanguage() == 'ar' ? 'وظائف' : 'Careers'; ?>
                    </a></li>
                </ul>
            </div>
        </div>

        <hr class="my-4">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted">
                    &copy; <?php echo date('Y'); ?> Wazfnee. 
                    <?php echo getCurrentLanguage() == 'ar' ? 'جميع الحقوق محفوظة' : 'All rights reserved'; ?>.
                </p>
            </div>
            <div class="col-md-6 text-end">
                <div class="app-download-links">
                    <a href="#" class="text-muted me-3">
                        <i class="fab fa-apple"></i> App Store
                    </a>
                    <a href="#" class="text-muted">
                        <i class="fab fa-google-play"></i> Google Play
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Alert Messages -->
<?php $alert = getAlert(); ?>
<?php if ($alert): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
    <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $alert['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>