<?php
// Thumbnail generation script with caching
require_once __DIR__ . '/../includes/config.php';

// Get parameters
$width = isset($_GET['w']) ? (int)$_GET['w'] : 300;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 200;
$imagePath = isset($_GET['img']) ? $_GET['img'] : '';

// Validate parameters
if ($width < 1 || $width > 2000 || $height < 1 || $height > 2000 || empty($imagePath)) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid parameters');
}

// Security: prevent directory traversal
$imagePath = ltrim($imagePath, '/');
if (strpos($imagePath, '..') !== false || strpos($imagePath, "\0") !== false) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid path');
}

// Full path to source image
$sourceFile = __DIR__ . '/../' . $imagePath;

if (!file_exists($sourceFile)) {
    header('HTTP/1.0 404 Not Found');
    exit('Image not found');
}

// Create cache directory
$cacheDir = __DIR__ . '/../cache/thumbnails/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Generate cache filename
$cacheFile = $cacheDir . md5($imagePath . $width . $height) . '.jpg';

// Check if cached thumbnail exists and is newer than source
if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile)) {
    // Serve cached file
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=31536000'); // 1 year
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
    readfile($cacheFile);
    exit;
}

// Get image info
$imageInfo = getimagesize($sourceFile);
if (!$imageInfo) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid image');
}

// Create image resource based on type
switch ($imageInfo[2]) {
    case IMAGETYPE_JPEG:
        $sourceImage = imagecreatefromjpeg($sourceFile);
        break;
    case IMAGETYPE_PNG:
        $sourceImage = imagecreatefrompng($sourceFile);
        break;
    case IMAGETYPE_GIF:
        $sourceImage = imagecreatefromgif($sourceFile);
        break;
    case IMAGETYPE_WEBP:
        $sourceImage = imagecreatefromwebp($sourceFile);
        break;
    default:
        header('HTTP/1.0 400 Bad Request');
        exit('Unsupported image type');
}

if (!$sourceImage) {
    header('HTTP/1.0 500 Internal Server Error');
    exit('Failed to load image');
}

$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);

// Calculate dimensions maintaining aspect ratio
$aspectRatio = $sourceWidth / $sourceHeight;
$targetAspectRatio = $width / $height;

if ($aspectRatio > $targetAspectRatio) {
    // Source is wider - fit to height
    $newHeight = $height;
    $newWidth = (int)($height * $aspectRatio);
} else {
    // Source is taller - fit to width
    $newWidth = $width;
    $newHeight = (int)($width / $aspectRatio);
}

// Create canvas
$thumbnail = imagecreatetruecolor($newWidth, $newHeight);

// Preserve transparency for PNG/GIF
if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
    imagealphablending($thumbnail, false);
    imagesavealpha($thumbnail, true);
    $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
    imagefill($thumbnail, 0, 0, $transparent);
}

// Resize with high quality
imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

// Save to cache
imagejpeg($thumbnail, $cacheFile, 85);

// Serve the thumbnail
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=31536000'); // 1 year
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
imagejpeg($thumbnail, null, 85);

// Clean up (GD automatically handles memory in PHP 8.0+)
if (function_exists('imagedestroy')) {
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
}