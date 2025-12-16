<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$baseUrl = 'https://stock.happymonkey.ai';

// Helper function to output URL
function outputUrl($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.7') {
    $lastmodStr = $lastmod ? $lastmod->format('Y-m-d\TH:i:s\Z') : date('Y-m-d\TH:i:s\Z');
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
    echo "    <lastmod>$lastmodStr</lastmod>\n";
    echo "    <changefreq>$changefreq</changefreq>\n";
    echo "    <priority>$priority</priority>\n";
    echo "  </url>\n";
}

// Homepage - highest priority
outputUrl($baseUrl, null, 'daily', '1.0');

// Static pages
$staticPages = [
    '/about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    '/contact' => ['priority' => '0.6', 'changefreq' => 'monthly']
];

foreach ($staticPages as $path => $config) {
    outputUrl($baseUrl . $path, null, $config['changefreq'], $config['priority']);
}

// Category pages
try {
    $categories = getCategories();
    foreach ($categories as $category) {
        if ($category['actual_count'] > 0) { // Only include categories with images
            $url = $baseUrl . '/category/' . urlencode($category['slug']);
            // Use a default lastmod for categories (could be enhanced to track category updates)
            $lastmod = new DateTime('2025-12-16');
            outputUrl($url, $lastmod, 'weekly', '0.8');
        }
    }
} catch (Exception $e) {
    error_log("Sitemap error fetching categories: " . $e->getMessage());
}

// Image pages (limited to most recent for performance)
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, c.slug as category_slug, c.name as category_name 
        FROM images i 
        LEFT JOIN categories c ON i.category_id = c.id 
        WHERE c.id IS NOT NULL
        ORDER BY i.created_at DESC 
        LIMIT 1000
    ");
    $stmt->execute();
    $images = $stmt->fetchAll();
    
    foreach ($images as $image) {
        if ($image['category_slug']) {
            $slug = createSlug($image['prompt']);
            $url = $baseUrl . '/image/' . $image['id'] . '/' . urlencode($slug);
            
            $lastmod = new DateTime($image['created_at']);
            
            // Higher priority for more recent/popular images
            $daysSinceCreated = (int)(new DateTime())->format('U') - (int)$lastmod->format('U');
            $daysSinceCreated = floor($daysSinceCreated / 86400); // Convert seconds to days
            $priority = $daysSinceCreated < 7 ? '0.9' : ($daysSinceCreated < 30 ? '0.7' : '0.6');
            
            outputUrl($url, $lastmod, 'monthly', $priority);
        }
    }
    
    // Log total images in sitemap
    error_log("Sitemap generated with " . count($images) . " images");
    
} catch (Exception $e) {
    error_log("Sitemap error fetching images: " . $e->getMessage());
}

echo '</urlset>';
?>