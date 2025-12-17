<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seo.php';

// Get category from URL
$slug = $_GET['slug'] ?? '';
if (empty($slug)) { header('Location: /'); exit; }

$category = getCategoryBySlug($slug);
if (!$category) { header('HTTP/1.0 404 Not Found'); echo "Category not found"; exit; }

// Sorting
$sort = $_GET['sort'] ?? 'recent';
if (!in_array($sort, ['alphabetical', 'recent', 'popular'])) { $sort = 'recent'; }

$images = getImagesByCategory($category['id'], null, 0, $sort);

$pageTitle = $category['name'];
$pageDescription = "Browse {$category['actual_count']} AI-generated images in the {$category['name']} category.";

// SEO for header
$SEO_PAGE_TYPE = 'category';
$SEO_PAGE_DATA = ['category' => $category, 'images' => $images];

include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo e($category['name']); ?></li>
        </ol>
    </nav>
</div>

<section class="py-4 bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3"><?php echo e($category['name']); ?></h1>
                <p class="text-muted mb-0"><i class="bi bi-images me-2"></i><?php echo count($images); ?> images in this category</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="/" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-2"></i>Back to Categories</a>
            </div>
        </div>
    </div>
</section>

<section class="py-3 bg-light">
    <div class="container">
        <div class="sort-controls">
            <div class="sort-buttons">
                <a href="?sort=recent" class="sort-btn <?php echo $sort === 'recent' ? 'active' : ''; ?>">Most Recent</a>
                <a href="?sort=popular" class="sort-btn <?php echo $sort === 'popular' ? 'active' : ''; ?>">Most Downloaded</a>
                <a href="?sort=alphabetical" class="sort-btn <?php echo $sort === 'alphabetical' ? 'active' : ''; ?>">Alphabetical</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if (empty($images)): ?>
            <div class="text-center py-5"><i class="bi bi-image display-1 text-muted"></i><p class="text-muted mt-3">No images found</p></div>
        <?php else: ?>
            <?php if ($sort === 'alphabetical'): ?>
                <?php
                $grouped = [];
                foreach ($images as $img) {
                    $L = strtoupper(substr($img['category_name'],0,1));
                    $grouped[$L][] = $img;
                }
                ksort($grouped);
                ?>
                <div class="categories-with-nav">
                    <div class="categories-container">
                        <?php foreach ($grouped as $letter => $items): ?>
                            <div class="category-section" id="letter-<?php echo $letter; ?>">
                                <div class="category-letter-header"><div class="category-letter"><?php echo $letter; ?></div>
                                    <div><h3 class="mb-1"><?php echo $letter; ?></h3><p class="category-count mb-0"><?php echo count($items); ?> images</p></div>
                                </div>
                                <div class="row g-4">
                                    <?php foreach ($items as $image): ?>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <a href="/image.php?id=<?php echo $image['id']; ?>" class="image-card text-decoration-none">
                                                <img data-src="/thumbnail.php?w=300&h=225&img=/images/<?php echo e($image['filename']); ?>" class="lazy" alt="<?php echo e($image['category_name']); ?>">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="alphabet-nav">
                        <?php foreach (range('A','Z') as $L):
                            $has = isset($grouped[$L]);
                            if ($has) {
                                echo "<a href=\"#letter-{$L}\" class=\"alpha-link alpha-letter has-categories\" data-letter=\"{$L}\">{$L}</a>";
                            } else {
                                echo "<span class=\"alpha-letter\">{$L}</span>";
                            }
                        endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="image-grid">
                    <?php foreach ($images as $image): ?>
                        <a href="/image.php?id=<?php echo $image['id']; ?>" class="image-card text-decoration-none">
                            <img data-src="/thumbnail.php?w=300&h=225&img=/images/<?php echo e($image['filename']); ?>" class="lazy" alt="<?php echo e($image['category_name']); ?>">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>