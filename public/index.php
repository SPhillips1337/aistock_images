<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Home';
$pageDescription = 'Browse thousands of AI-generated stock photos across various categories. Free high-quality images for your projects.';

// Get all categories
$categories = getCategories();
$recentImages = getRecentImages(8);

include __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-4">Discover AI-Generated Stock Photos</h1>
        <p class="lead mb-4">
            Thousands of high-quality, unique images created by artificial intelligence. 
            Perfect for your creative projects.
        </p>
        
        <!-- Search Bar -->
        <div class="search-bar-large">
            <form action="/search.php" method="GET" class="d-flex">
                <input type="search" name="q" class="form-control" 
                       placeholder="Search for images..." required>
                <button type="submit" class="btn btn-light">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Recent Images Section -->
<?php if (!empty($recentImages)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Recently Added</h2>
            <p class="text-muted mt-3">Fresh AI-generated images from our latest collection</p>
        </div>
        
        <div class="image-grid">
            <?php foreach ($recentImages as $image): ?>
                <a href="/image.php?id=<?php echo $image['id']; ?>" class="image-card text-decoration-none">
                    <img data-src="/images/<?php echo e($image['filename']); ?>" 
                         class="lazy"
                         alt="<?php echo e($image['category_name']); ?>">
                    <div class="image-overlay">
                        <div class="image-overlay-content">
                            <small><?php echo e($image['category_name']); ?></small>
                        </div>
                    </div>
                    <button class="download-btn" onclick="event.preventDefault(); trackDownload(<?php echo $image['id']; ?>); window.location.href='/download.php?id=<?php echo $image['id']; ?>';">
                        <i class="bi bi-download"></i>
                    </button>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Categories Section -->
<section class="py-5 bg-white" id="categories">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Browse by Category</h2>
            <p class="text-muted mt-3">Explore our diverse collection of AI-generated images</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <?php if ($category['actual_count'] > 0): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <a href="/category.php?slug=<?php echo e($category['slug']); ?>" 
                           class="text-decoration-none">
                            <div class="category-card">
                                <?php if ($category['thumbnail_path']): ?>
                                    <img data-src="/images/<?php echo e($category['thumbnail_path']); ?>" 
                                         class="lazy"
                                         alt="<?php echo e($category['name']); ?>">
                                <?php else: ?>
                                    <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                                <?php endif; ?>
                                <div class="category-card-body">
                                    <h3 class="category-card-title"><?php echo e($category['name']); ?></h3>
                                    <p class="category-card-count">
                                        <i class="bi bi-images me-1"></i>
                                        <?php echo $category['actual_count']; ?> 
                                        <?php echo $category['actual_count'] == 1 ? 'image' : 'images'; ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h4 class="fw-bold mb-3">AI-Generated</h4>
                    <p class="text-muted">
                        All images are created using advanced AI technology, 
                        ensuring unique and creative visuals.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-3">High Quality</h4>
                    <p class="text-muted">
                        Every image is generated at high resolution, 
                        perfect for professional projects.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-infinity"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Always Growing</h4>
                    <p class="text-muted">
                        New images are added daily, expanding our 
                        collection across all categories.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
