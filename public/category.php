<?php
require_once __DIR__ . '/../includes/config.php';

// Get category from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /');
    exit;
}

$category = getCategoryBySlug($slug);

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    echo "Category not found";
    exit;
}

// Get sorting preference
$sort = $_GET['sort'] ?? 'recent';
if (!in_array($sort, ['alphabetical', 'recent', 'popular'])) {
    $sort = 'recent';
}

$images = getImagesByCategory($category['id'], null, 0, $sort);

$pageTitle = $category['name'];
$pageDescription = "Browse {$category['actual_count']} AI-generated images in the {$category['name']} category.";

include __DIR__ . '/../includes/header.php';
?>
</head>

<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($category['name']); ?></li>
        </ol>
    </nav>
</div>

<!-- Category Header -->
<section class="py-4 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3"><?php echo e($category['name']); ?></h1>
                <p class="text-muted mb-0">
                    <i class="bi bi-images me-2"></i>
                    <?php echo count($images); ?> 
                    <?php echo count($images) == 1 ? 'image' : 'images'; ?> 
                    in this category
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="/" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Categories
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Sort Controls -->
<section class="py-3 bg-light">
    <div class="container">
        <div class="sort-controls">
            <div class="sort-buttons">
                <a href="?sort=recent" class="sort-btn <?php echo $sort === 'recent' ? 'active' : ''; ?>" data-sort="recent">
                    <i class="bi bi-clock-history me-1"></i> Most Recent
                </a>
                <a href="?sort=popular" class="sort-btn <?php echo $sort === 'popular' ? 'active' : ''; ?>" data-sort="popular">
                    <i class="bi bi-bar-chart me-1"></i> Most Downloaded
                </a>
                <a href="?sort=alphabetical" class="sort-btn <?php echo $sort === 'alphabetical' ? 'active' : ''; ?>" data-sort="alphabetical">
                    <i class="bi bi-sort-alpha-down me-1"></i> Alphabetical
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Images Grid -->
<section class="py-5">
    <div class="container">
        <?php if (empty($images)): ?>
            <div class="text-center py-5">
                <i class="bi bi-image display-1 text-muted"></i>
                <p class="text-muted mt-3">No images found in this category yet.</p>
                <a href="/" class="btn btn-primary mt-3">Browse Other Categories</a>
            </div>
        <?php else: ?>
            <div class="image-grid">
                <?php foreach ($images as $image): ?>
                    <a href="/image.php?id=<?php echo $image['id']; ?>" class="image-card text-decoration-none">
                        <img data-src="/thumbnail.php?w=300&h=225&img=/images/<?php echo e($image['filename']); ?>" 
                             class="lazy"
                             alt="<?php echo e($category['name']); ?> - Image <?php echo $image['id']; ?>">
                        <div class="image-overlay">
                            <div class="image-overlay-content">
                                <small>
                                    <i class="bi bi-download me-1"></i>
                                    <?php echo $image['downloads']; ?> downloads
                                </small>
                            </div>
                        </div>
                        <button class="download-btn" onclick="event.preventDefault(); trackDownload(<?php echo $image['id']; ?>); window.location.href='/download.php?id=<?php echo $image['id']; ?>';">
                            <i class="bi bi-download"></i>
                        </button>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>