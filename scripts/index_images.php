#!/usr/bin/env php
<?php
/**
 * Image Indexer Script
 * Scans the images directory and populates the database with image metadata
 */

require_once __DIR__ . '/../includes/config.php';

echo "Starting image indexing...\n";

// Initialize database
$db = getDB();

// Create tables from schema
$schema = file_get_contents(__DIR__ . '/../schema.sql');
$db->exec($schema);
echo "Database schema initialized.\n";

// Scan images directory
$imagesPath = IMAGES_DIR;
if (!is_dir($imagesPath)) {
    die("Images directory not found: $imagesPath\n");
}

$files = glob($imagesPath . '*.{png,jpg,jpeg}', GLOB_BRACE);
echo "Found " . count($files) . " image files.\n";

$categories = [];
$imageCount = 0;

foreach ($files as $filepath) {
    $filename = basename($filepath);
    
    // Skip rejected images
    if (strpos($filename, '.rejected') !== false) {
        continue;
    }
    
    // Extract category from filename
    // Format: Category_Name.png or Category_Name_1.png or Category_Name_ovis.png
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    // Remove suffixes first, then trailing numbers
    $categoryName = preg_replace('/_ovis$/', '', $name);
    $categoryName = preg_replace('/_flux$/', '', $categoryName);
    $categoryName = preg_replace('/_\d+$/', '', $categoryName);
    
    // Replace underscores with spaces for display
    $categoryDisplay = str_replace('_', ' ', $categoryName);
    $categorySlug = createSlug($categoryDisplay);
    
    // Determine model used
    if (strpos($filename, '_ovis') !== false) {
        $model = 'ovis';
    } elseif (strpos($filename, '_flux') !== false) {
        $model = 'Flux2';
    } else {
        $model = 'turbo';
    }
    
    // Create or get category
    if (!isset($categories[$categorySlug])) {
        // Check if category exists
        $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$categorySlug]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $categoryId = $existing['id'];
        } else {
            // Create new category
            $stmt = $db->prepare("
                INSERT INTO categories (name, slug, thumbnail_path) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$categoryDisplay, $categorySlug, $filename]);
            $categoryId = $db->lastInsertId();
            echo "Created category: $categoryDisplay\n";
        }
        
        $categories[$categorySlug] = $categoryId;
    } else {
        $categoryId = $categories[$categorySlug];
    }
    
    // Check if image already exists
    $stmt = $db->prepare("SELECT id, created_at FROM images WHERE filename = ?");
    $stmt->execute([$filename]);
    $existing = $stmt->fetch();
    if ($existing) {
        // If file on disk is newer than DB created_at, update the DB timestamp so it appears in "Recently Added"
        $file_mtime = filemtime($filepath);
        $db_time = strtotime($existing['created_at']);
        if ($file_mtime && $file_mtime > $db_time) {
            $new_time = date('Y-m-d H:i:s', $file_mtime);
            $upd = $db->prepare("UPDATE images SET created_at = ? WHERE id = ?");
            $upd->execute([$new_time, $existing['id']]);
            echo "Updated timestamp for existing image: $filename -> $new_time\n";
        }
        continue; // Skip further insertion
    }
    
    // Get image dimensions
    $imageInfo = getimagesize($filepath);
    $width = $imageInfo[0] ?? 1024;
    $height = $imageInfo[1] ?? 1024;
    
    // Insert image
    $stmt = $db->prepare("
        INSERT INTO images (filename, filepath, category_id, model, width, height, prompt) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $prompt = "stock photography of $categoryDisplay, high quality, 4k, photorealistic";
    $stmt->execute([$filename, $filepath, $categoryId, $model, $width, $height, $prompt]);
    $imageId = $db->lastInsertId();
    
    // Create tags from category name
    $tags = explode(' ', $categoryDisplay);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (strlen($tag) > 2) {
            $stmt = $db->prepare("INSERT INTO tags (image_id, tag_name) VALUES (?, ?)");
            $stmt->execute([$imageId, strtolower($tag)]);
        }
    }
    
    $imageCount++;
}

// Update category image counts
$stmt = $db->query("
    UPDATE categories 
    SET image_count = (
        SELECT COUNT(*) FROM images WHERE category_id = categories.id
    )
");

echo "\nIndexing complete!\n";
echo "Total images indexed: $imageCount\n";
echo "Total categories: " . count($categories) . "\n";

// Show summary
echo "\nCategory Summary:\n";
$stmt = $db->query("SELECT name, image_count FROM categories ORDER BY name");
while ($row = $stmt->fetch()) {
    echo "  - {$row['name']}: {$row['image_count']} images\n";
}
