<?php
require_once __DIR__ . '/../includes/config.php';

// Get search query
$query = $_GET['q'] ?? '';

$results = [];
if (!empty($query)) {
    $results = searchImages($query);
}

$pageTitle = !empty($query) ? "Search results for: $query" : 'Search';
$pageDescription = "Search our collection of AI-generated stock photos.";

include __DIR__ . '/../includes/header.php';
?>

<!-- Search Header -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-4 text-center">Search Images</h1>
                
                <!-- Search Form -->
                <form action="/search.php" method="GET" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input type="search" name="q" class="form-control" 
                               placeholder="Search for images..." 
                               value="<?php echo e($query); ?>" 
                               required autofocus>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-search me-2"></i>Search
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($query)): ?>
                    <p class="text-muted text-center">
                        Found <?php echo count($results); ?> 
                        <?php echo count($results) == 1 ? 'result' : 'results'; ?> 
                        for "<?php echo e($query); ?>"
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Search Results -->
<section class="py-5">
    <div class="container">
        <?php if (empty($query)): ?>
            <!-- Popular Categories -->
            <div class="text-center mb-5">
                <h3 class="h5 fw-bold mb-4">Popular Searches</h3>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <?php
                    $popularCategories = array_slice(getCategories(), 0, 10);
                    foreach ($popularCategories as $cat):
                    ?>
                        <a href="/search.php?q=<?php echo urlencode($cat['name']); ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <?php echo e($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
        <?php elseif (empty($results)): ?>
            <!-- No Results -->
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h3 class="mt-4 mb-3">No results found</h3>
                <p class="text-muted mb-4">
                    We couldn't find any images matching "<?php echo e($query); ?>". 
                    Try different keywords or browse our categories.
                </p>
                <a href="/" class="btn btn-primary">Browse Categories</a>
            </div>
            
        <?php else: ?>
            <!-- Results Grid -->
            <div class="image-grid">
                <?php foreach ($results as $image): ?>
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
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
