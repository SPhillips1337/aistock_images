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
                <div class="image-detail-main position-relative">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" class="d-block cursor-zoom-in">
                        <img src="/images/<?php echo e($image['filename']); ?>" 
                             alt="<?php echo e($image['category_name']); ?> - Image #<?php echo $image['id']; ?>"
                             class="img-fluid rounded shadow-sm">
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-dark bg-opacity-50 p-2 rounded-circle">
                                <i class="bi bi-arrows-fullscreen text-white"></i>
                            </span>
                        </div>
                    </a>
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

                    <!-- Share Buttons -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted mb-2">Share</label>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                               target="_blank" 
                               class="btn btn-outline-secondary flex-grow-1"
                               title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode("Check out this AI generated image of " . $image['category_name']); ?>" 
                               target="_blank" 
                               class="btn btn-outline-secondary flex-grow-1"
                               title="Share on X (Twitter)">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <button class="btn btn-outline-secondary flex-grow-1" id="shareBtn" title="Share or Copy Link">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Image Meta -->
                    <div class="image-meta flex-column align-items-start border-top pt-4">
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

<!-- Image Lightbox Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 position-absolute top-0 end-0 z-3 p-4">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center p-0">
                <img src="/images/<?php echo e($image['filename']); ?>" 
                     class="img-fluid" 
                     style="max-height: 100vh; max-width: 100vw; object-fit: contain;" 
                     alt="<?php echo e($image['category_name']); ?>">
            </div>
        </div>
    </div>
</div>

<!-- Related Images -->
<?php if (!empty($relatedImages)): ?>
<section class="py-5 bg-white related-section">
    <div class="container">
        <h3 class="h4 fw-bold mb-4">More from <?php echo e($image['category_name']); ?></h3>
        
        <div class="row g-3 related-images-grid">
            <?php foreach ($relatedImages as $related): ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <a href="/image.php?id=<?php echo $related['id']; ?>" class="image-card related-image-card text-decoration-none">
                        <img src="/thumbnail.php?w=200&h=150&img=/images/<?php echo e($related['filename']); ?>" 
                             alt="Related image">
                        <div class="image-overlay">
                            <div class="image-overlay-content">
                                <div class="overlay-center">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                    <span class="overlay-text">View</span>
                                </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            const shareData = {
                title: '<?php echo e($pageTitle); ?>',
                text: 'Check out this AI-generated image from HappyMonkey!',
                url: window.location.href
            };

            if (navigator.share) {
                try {
                    await navigator.share(shareData);
                } catch (err) {
                    console.log('Error sharing:', err);
                }
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    // Visual feedback
                    const originalIcon = shareBtn.innerHTML;
                    shareBtn.innerHTML = '<i class="bi bi-check-lg"></i>';
                    shareBtn.classList.add('btn-success');
                    shareBtn.classList.remove('btn-outline-secondary');
                    
                    setTimeout(() => {
                        shareBtn.innerHTML = originalIcon;
                        shareBtn.classList.remove('btn-success');
                        shareBtn.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            }
        });
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
