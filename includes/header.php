<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? e($pageDescription) : 'Browse thousands of AI-generated stock photos across various categories. Free high-quality images for your projects.'; ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/lazy-load.css">
    <link rel="stylesheet" href="/assets/css/cookie-consent.css">
    <?php 
    // Include filter controls CSS and JS for pages that need it
    if (basename($_SERVER['PHP_SELF']) === 'index.php' || basename($_SERVER['PHP_SELF']) === 'category.php'):
    ?>
        <link rel="stylesheet" href="/assets/css/filter-controls.css?v=1.0.1">
        <script src="/assets/js/filter-controls.js" defer></script>
    <?php endif; ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-BHCYC02Q5P"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-BHCYC02Q5P');
    </script>    
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-image-fill text-primary me-2"></i>
                <span class="gradient-text"><?php echo SITE_NAME; ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <?php
                            $navCategories = getCategories();
                            $maxNav = 10;
                            foreach (array_slice($navCategories, 0, $maxNav) as $cat):
                            ?>
                                <li><a class="dropdown-item" href="/category.php?slug=<?php echo e($cat['slug']); ?>"><?php echo e($cat['name']); ?></a></li>
                            <?php endforeach; ?>
                            <?php if (count($navCategories) > $maxNav): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/#categories">View All Categories</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact.php">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="legalDropdown" role="button" data-bs-toggle="dropdown">
                            Legal
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="legalDropdown">
                            <li><a class="dropdown-item" href="/privacy-policy.php">Privacy Policy</a></li>
                            <li><a class="dropdown-item" href="/terms-of-service.php">Terms of Service</a></li>
                        </ul>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <form action="/search.php" method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="search" name="q" class="form-control" placeholder="Search images..." 
                                       value="<?php echo isset($_GET['q']) ? e($_GET['q']) : ''; ?>" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- GDPR Cookie Consent Banner -->
    <div id="cookieConsent" class="cookie-consent-banner">
        <div class="container">
            <div class="cookie-consent-content">
                <div class="cookie-consent-text">
                    <h4 class="h6 mb-2">🍪 Cookie Notice</h4>
                    <p class="mb-0">We use cookies to enhance your experience, analyze site traffic, and personalize content. By continuing to use our site, you agree to our use of cookies.</p>
                </div>
                <div class="cookie-consent-buttons">
                    <button type="button" class="btn btn-outline-primary btn-sm me-2" id="cookieSettings">
                        <i class="bi bi-gear me-1"></i>Settings
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="acceptAllCookies">
                        Accept All
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ms-2" id="acceptEssentialCookies">
                        Essential Only
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cookie Settings Modal -->
    <div class="modal fade" id="cookieSettingsModal" tabindex="-1" aria-labelledby="cookieSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cookieSettingsModalLabel">
                        <i class="bi bi-gear me-2"></i>Cookie Preferences
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="fw-bold">Essential Cookies</h6>
                        <p class="text-muted small mb-2">Required for the website to function properly.</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="essentialCookies" checked disabled>
                            <label class="form-check-label" for="essentialCookies">
                                Essential (Always Active)
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">Analytics Cookies</h6>
                        <p class="text-muted small mb-2">Help us understand how visitors interact with our website.</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="analyticsCookies">
                            <label class="form-check-label" for="analyticsCookies">
                                Analytics
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">Marketing Cookies</h6>
                        <p class="text-muted small mb-2">Used to deliver advertisements relevant to you.</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="marketingCookies">
                            <label class="form-check-label" for="marketingCookies">
                                Marketing
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCookieSettings">Save Preferences</button>
                </div>
            </div>
        </div>
    </div>