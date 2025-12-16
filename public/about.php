<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'About';
$pageDescription = 'Learn about our AI-generated stock photo collection and how we create unique images.';

include __DIR__ . '/../includes/header.php';
?>

<!-- About Header -->
<section class="py-5 bg-gradient text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">About <?php echo SITE_NAME; ?></h1>
        <p class="lead">Creating the future of stock photography with AI</p>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-5 rounded shadow-sm">
                    <h2 class="h3 fw-bold mb-4">Our Mission</h2>
                    <p class="lead text-muted mb-4">
                        We're revolutionizing stock photography by harnessing the power of 
                        artificial intelligence to create unique, high-quality images.
                    </p>
                    
                    <h3 class="h4 fw-bold mt-5 mb-3">How It Works</h3>
                    <p class="mb-4">
                        Our images are generated using state-of-the-art AI models that create 
                        photorealistic images from text descriptions. Each image is unique and 
                        has never existed before, offering you truly original content for your projects.
                    </p>
                    
                    <div class="row g-4 my-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold">High Quality</h5>
                                    <p class="text-muted mb-0">
                                        All images are generated at 1024×1024 resolution or higher
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold">Unique Content</h5>
                                    <p class="text-muted mb-0">
                                        Every image is one-of-a-kind, created specifically by our AI
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold">Diverse Categories</h5>
                                    <p class="text-muted mb-0">
                                        From nature to technology, we cover all major stock photo categories
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold">Daily Updates</h5>
                                    <p class="text-muted mb-0">
                                        New images are added automatically every day
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="h4 fw-bold mt-5 mb-3">The Technology</h3>
                    <p class="mb-4">
                        We use multiple AI models including Z-Image Turbo and Ovis text-to-image generators. 
                        Each image goes through quality checks to ensure it meets our standards before 
                        being added to the collection.
                    </p>
                    
                    <h3 class="h4 fw-bold mt-5 mb-3">Usage Rights</h3>
                    <p class="mb-4">
                        All images on this site are AI-generated and can be downloaded for your projects. 
                        Since these images are created by AI, they don't contain copyrighted material or 
                        recognizable individuals.
                    </p>
                    
                    <div class="text-center mt-5">
                        <a href="/" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-grid me-2"></i>Browse Images
                        </a>
                        <a href="/contact.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-envelope me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
