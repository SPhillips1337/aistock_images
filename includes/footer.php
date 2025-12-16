    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-image-fill text-primary me-2"></i>
                        <?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-white-50">
                        Discover thousands of high-quality AI-generated stock photos. 
                        Perfect for your creative projects, presentations, and designs.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-4 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-white-50 text-decoration-none hover-primary">Home</a></li>
                        <li class="mb-2"><a href="/#categories" class="text-white-50 text-decoration-none hover-primary">Categories</a></li>
                        <li class="mb-2"><a href="/about.php" class="text-white-50 text-decoration-none hover-primary">About</a></li>
                        <li class="mb-2"><a href="/contact.php" class="text-white-50 text-decoration-none hover-primary">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Popular Categories</h6>
                    <ul class="list-unstyled">
                        <?php
                        $footerCategories = getCategories();
                        foreach (array_slice($footerCategories, 0, 5) as $cat):
                        ?>
                            <li class="mb-2">
                                <a href="/category.php?slug=<?php echo e($cat['slug']); ?>" 
                                   class="text-white-50 text-decoration-none hover-primary">
                                    <?php echo e($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-4">
                    <h6 class="fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-3 mb-3">
                        <a href="#" class="text-white-50 fs-4 hover-primary"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white-50 fs-4 hover-primary"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white-50 fs-4 hover-primary"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white-50 fs-4 hover-primary"><i class="bi bi-linkedin"></i></a>
                    </div>
                    <p class="text-white-50 small">
                        <i class="bi bi-envelope me-2"></i>
                        <?php echo CONTACT_EMAIL; ?>
                    </p>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0 text-white-50 small">
                        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All images are AI-generated.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-white-50 small">
                        Made with <i class="bi bi-heart-fill text-danger"></i> using AI
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/lazy-load.js"></script>
</body>
</html>
