<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Contact';
$pageDescription = 'Get in touch with us for questions, feedback, or collaboration opportunities.';

include __DIR__ . '/../includes/header.php';
?>

<!-- Contact Header -->
<section class="py-5 bg-gradient text-white">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
        <p class="lead">We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row g-4 mb-5">
                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded shadow-sm text-center h-100">
                            <div class="fs-1 text-primary mb-3">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Email Us</h4>
                            <p class="text-muted mb-3">
                                Send us an email and we'll get back to you as soon as possible.
                            </p>
                            <a href="mailto:<?php echo CONTACT_EMAIL; ?>" class="btn btn-outline-primary">
                                <?php echo CONTACT_EMAIL; ?>
                            </a>
                        </div>
                    </div>
                    
                    <!-- General Info -->
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded shadow-sm text-center h-100">
                            <div class="fs-1 text-primary mb-3">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Quick Info</h4>
                            <p class="text-muted mb-3">
                                Browse our about page to learn more about our AI-generated images.
                            </p>
                            <a href="/about.php" class="btn btn-outline-primary">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="bg-white p-5 rounded shadow-sm">
                    <h2 class="h3 fw-bold mb-4 text-center">Send us a Message</h2>
                    
                    <form action="mailto:<?php echo CONTACT_EMAIL; ?>?subject=Contact from <?php echo SITE_NAME; ?>" method="post" enctype="text/plain">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Your Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="subject" class="form-label fw-semibold">Subject</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Choose a subject...</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Image Request">Image Request</option>
                                    <option value="Technical Issue">Technical Issue</option>
                                    <option value="Partnership">Partnership Opportunity</option>
                                    <option value="Feedback">Feedback</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label fw-semibold">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="alert alert-info mt-4 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> This form will open your default email client. 
                        Alternatively, you can email us directly at 
                        <a href="mailto:<?php echo CONTACT_EMAIL; ?>"><?php echo CONTACT_EMAIL; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
