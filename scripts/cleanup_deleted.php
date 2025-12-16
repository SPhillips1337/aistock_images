#!/usr/bin/env php
<?php
/**
 * Cleanup Script
 * Removes database entries for deleted image files and fixes broken category thumbnails
 */

require_once __DIR__ . '/../includes/config.php';

echo "Starting cleanup of deleted images...\n";

$db = getDB();
$deletedCount = 0;

// Get all images from database
$stmt = $db->query("SELECT id, filename, filepath FROM images");
$images = $stmt->fetchAll();

foreach ($images as $image) {
    $fullPath = $image['filepath'];
    
    // Check if file exists
    if (!file_exists($fullPath)) {
        echo "Removing deleted image: {$image['filename']}\n";
        
        // Delete tags first (foreign key constraint)
        $deleteTagsStmt = $db->prepare("DELETE FROM tags WHERE image_id = ?");
        $deleteTagsStmt->execute([$image['id']]);
        
        // Delete image record
        $deleteImageStmt = $db->prepare("DELETE FROM images WHERE id = ?");
        $deleteImageStmt->execute([$image['id']]);
        
        $deletedCount++;
    }
}

// Fix broken category thumbnails
echo "Checking category thumbnails...\n";
$stmt = $db->query("SELECT id, name, thumbnail_path FROM categories WHERE thumbnail_path IS NOT NULL");
$categories = $stmt->fetchAll();
$fixedThumbnails = 0;

foreach ($categories as $category) {
    $thumbnailPath = IMAGES_DIR . $category['thumbnail_path'];
    
    if (!file_exists($thumbnailPath)) {
        echo "Fixing broken thumbnail for category: {$category['name']}\n";
        
        // Find a replacement image from this category
        $replaceStmt = $db->prepare("SELECT filename FROM images WHERE category_id = ? LIMIT 1");
        $replaceStmt->execute([$category['id']]);
        $replacement = $replaceStmt->fetch();
        
        if ($replacement) {
            $updateStmt = $db->prepare("UPDATE categories SET thumbnail_path = ? WHERE id = ?");
            $updateStmt->execute([$replacement['filename'], $category['id']]);
            echo "  Updated to: {$replacement['filename']}\n";
            $fixedThumbnails++;
        } else {
            // No images in category, set thumbnail to null
            $updateStmt = $db->prepare("UPDATE categories SET thumbnail_path = NULL WHERE id = ?");
            $updateStmt->execute([$category['id']]);
            echo "  No images found, cleared thumbnail\n";
            $fixedThumbnails++;
        }
    }
}

// Update category image counts
$db->exec("
    UPDATE categories 
    SET image_count = (
        SELECT COUNT(*) FROM images WHERE category_id = categories.id
    )
");

// Remove empty categories
$emptyStmt = $db->prepare("DELETE FROM categories WHERE image_count = 0");
$emptyStmt->execute();
$emptyCategoriesDeleted = $emptyStmt->rowCount();

echo "Cleanup complete!\n";
echo "Deleted images removed: $deletedCount\n";
echo "Fixed category thumbnails: $fixedThumbnails\n";
echo "Empty categories removed: $emptyCategoriesDeleted\n";
