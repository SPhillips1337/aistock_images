<?php
// Database configuration
define('DB_PATH', __DIR__ . '/../stock_photos.db');
define('IMAGES_DIR', __DIR__ . '/../images/');
define('SITE_NAME', 'AI Stock Photos');
define('SITE_URL', 'http://localhost'); // Update for production
define('CONTACT_EMAIL', 'info@happymonkey.ai');

// Filter out images with missing files
function filterExistingImages($images) {
    return array_filter($images, function($image) {
        return file_exists($image['filepath']);
    });
}

// Initialize database connection
function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Enable write mode for SQLite
        $db->exec('PRAGMA journal_mode=WAL;');
        return $db;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Get all categories with image counts
function getCategories($sort = 'alphabetical') {
    $db = getDB();
    $orderClause = 'c.name ASC';
    
    switch($sort) {
        case 'recent':
            $orderClause = 'MAX(i.created_at) DESC';
            break;
        case 'popular':
            $orderClause = 'SUM(COALESCE(i.downloads, 0)) DESC';
            break;
        case 'alphabetical':
        default:
            $orderClause = 'c.name ASC';
            break;
    }
    
    $stmt = $db->query("
        SELECT c.*, COUNT(i.id) as actual_count,
               MAX(i.created_at) as latest_image,
               SUM(COALESCE(i.downloads, 0)) as total_downloads
        FROM categories c 
        LEFT JOIN images i ON c.id = i.category_id 
        GROUP BY c.id 
        ORDER BY $orderClause
    ");
    return $stmt->fetchAll();
}

// Get category by slug
function getCategoryBySlug($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// Get images by category
function getImagesByCategory($categoryId, $limit = null, $offset = 0, $sort = 'recent') {
    $db = getDB();
    $orderClause = 'i.created_at DESC';
    
    switch($sort) {
        case 'alphabetical':
            $orderClause = 'i.filename ASC';
            break;
        case 'popular':
            $orderClause = 'i.downloads DESC, i.created_at DESC';
            break;
        case 'recent':
        default:
            $orderClause = 'i.created_at DESC';
            break;
    }
    
    $sql = "SELECT * FROM images i WHERE category_id = ? ORDER BY $orderClause";
    if ($limit) {
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$categoryId, $limit * 2, $offset]);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->execute([$categoryId]);
    }
    $images = $stmt->fetchAll();
    $filtered = filterExistingImages($images);
    return $limit ? array_slice($filtered, 0, $limit) : $filtered;
}

// Get single image by ID
function getImageById($id) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, c.name as category_name, c.slug as category_slug 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        WHERE i.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Get related images (same category, excluding current)
function getRelatedImages($categoryId, $excludeId, $limit = 6) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT * FROM images 
        WHERE category_id = ? AND id != ? 
        ORDER BY RANDOM() 
        LIMIT ?
    ");
    $stmt->execute([$categoryId, $excludeId, $limit]);
    return $stmt->fetchAll();
}

// Search images
function searchImages($query, $limit = 50) {
    $db = getDB();
    $searchTerm = '%' . $query . '%';
    $stmt = $db->prepare("
        SELECT DISTINCT i.*, c.name as category_name, c.slug as category_slug 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        LEFT JOIN tags t ON i.id = t.image_id 
        WHERE i.filename LIKE ? 
           OR i.prompt LIKE ? 
           OR t.tag_name LIKE ? 
           OR c.name LIKE ?
        ORDER BY i.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit * 2]);
    $images = $stmt->fetchAll();
    $filtered = filterExistingImages($images);
    return array_slice($filtered, 0, $limit);
}

// Increment download counter
function incrementDownloads($imageId) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE images SET downloads = downloads + 1 WHERE id = ?");
    return $stmt->execute([$imageId]);
}

// Get recent images
function getRecentImages($limit = 12) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, c.name as category_name, c.slug as category_slug 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        ORDER BY i.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit * 2]); // Get more to account for missing files
    $images = $stmt->fetchAll();
    $filtered = filterExistingImages($images);
    return array_slice($filtered, 0, $limit);
}

// Get popular images (by downloads)
function getPopularImages($limit = 12) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, c.name as category_name, c.slug as category_slug 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        ORDER BY i.downloads DESC, i.created_at DESC 
        LIMIT ?
    ");
    $stmt->execute([$limit * 2]);
    $images = $stmt->fetchAll();
    $filtered = filterExistingImages($images);
    return array_slice($filtered, 0, $limit);
}

// Helper function to create slug from name
function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

// Get AI-suggested related images for an image
function getAIRelatedImages($imageId, $limit = 6) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, c.name as category_name, c.slug as category_slug, r.relevance_score
        FROM related_images r
        JOIN images i ON r.related_image_id = i.id
        LEFT JOIN categories c ON i.category_id = c.id
        WHERE r.image_id = ?
        ORDER BY r.relevance_score DESC, i.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$imageId, $limit * 2]);
    $images = $stmt->fetchAll();
    $filtered = filterExistingImages($images);
    return array_slice($filtered, 0, $limit);
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Get alphabet navigation for categories
function getAlphabetNavigation($categories) {
    $alphabet = range('A', 'Z');
    $nav = [];
    
    foreach ($alphabet as $letter) {
        $nav[$letter] = [
            'count' => 0,
            'categories' => [],
            'active' => false
        ];
    }
    
    foreach ($categories as $category) {
        $firstLetter = strtoupper(substr($category['name'], 0, 1));
        if (isset($nav[$firstLetter])) {
            $nav[$firstLetter]['count']++;
            $nav[$firstLetter]['categories'][] = $category['name'];
        }
    }
    
    return $nav;
}

// Escape output for security
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
