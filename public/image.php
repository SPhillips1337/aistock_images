<?php
require_once __DIR__ . '/../includes/config.php';

// Get image ID from URL
$imageId = $_GET['id'] ?? 0;

if (empty($imageId)) {
    header('Location: /');
    exit;
}

$image = getImageById($imageId);

if (!$image) {
    header('HTTP/1.0 404 Not Found');
    echo "Image not found";
    exit;
}

// Get AI-suggested related images first, fallback to category-based
$relatedImages = getAIRelatedImages($image['id'], 6);
if (empty($relatedImages)) {
    $relatedImages = getRelatedImages($image['category_id'], $image['id'], 6);
}

// Get file info
$filepath = IMAGES_DIR . $image['filename'];
$filesize = file_exists($filepath) ? filesize($filepath) : 0;

$pageTitle = $image['category_name'] . ' - Image #' . $image['id'];
$pageDescription = "Download high-quality AI-generated image from the {$image['category_name']} category.";

include __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">
                <a href="/category.php?slug=<?php echo e($image['category_slug']); ?>">
                    <?php echo e($image['category_name']); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Image #<?php echo $image['id']; ?></li>
        </ol>
    </nav>
</div>

<!-- Image Detail -->
<section class="py-4">
    <div class="container image-detail-container">
        <div class="row g-4">
            <!-- Main Image -->
            <div class="col-lg-8">
                <div class="image-detail-main">
                    <img src="/images/<?php echo e($image['filename']); ?>" 
                         alt="<?php echo e($image['category_name']); ?> - Image #<?php echo $image['id']; ?>"
                         class="img-fluid">
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded shadow-sm">
                    <h2 class="h4 fw-bold mb-4"><?php echo e($image['category_name']); ?></h2>
                    
                    <!-- Download Button -->
                    <a href="/download.php?id=<?php echo $image['id']; ?>" 
                       class="btn btn-primary w-100 mb-3"
                       onclick="trackDownload(<?php echo $image['id']; ?>)">
                        <i class="bi bi-download me-2"></i>Download Image
                    </a>
                    
                    <a href="/category.php?slug=<?php echo e($image['category_slug']); ?>" 
                       class="btn btn-outline-primary w-100 mb-4">
                        <i class="bi bi-grid me-2"></i>View Category
                    </a>
                    
                    <!-- Image Meta -->
                    <div class="image-meta flex-column align-items-start">
                        <div class="meta-item">
                            <i class="bi bi-aspect-ratio"></i>
                            <span><?php echo $image['width']; ?> × <?php echo $image['height']; ?> px</span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-file-earmark"></i>
                            <span><?php echo formatFileSize($filesize); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-download"></i>
                            <span><?php echo $image['downloads']; ?> downloads</span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-calendar"></i>
                            <span><?php echo date('M j, Y', strtotime($image['created_at'])); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <i class="bi bi-cpu"></i>
                            <span>Model: <?php echo ucfirst($image['model']); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($image['prompt']): ?>
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="fw-bold mb-2">Generation Prompt</h6>
                            <p class="small text-muted mb-0"><?php echo e($image['prompt']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Images -->
<?php if (!empty($relatedImages)): ?>
<section class="py-5 bg-white">
    <div class="container">
        <h3 class="h4 fw-bold mb-4">More from <?php echo e($image['category_name']); ?></h3>
        
        <div class="row g-3">
            <?php foreach ($relatedImages as $related): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <a href="/image.php?id=<?php echo $related['id']; ?>" class="image-card text-decoration-none">
                        <img src="/images/<?php echo e($related['filename']); ?>" 
                             alt="Related image">
                        <div class="image-overlay">
                            <div class="image-overlay-content">
                                <small><i class="bi bi-eye"></i></small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="/category.php?slug=<?php echo e($image['category_slug']); ?>" 
               class="btn btn-outline-primary">
                View All in <?php echo e($image['category_name']); ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
