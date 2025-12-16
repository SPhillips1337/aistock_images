<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/config.php';

// Get image ID
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

// Increment download counter - skip if fails to avoid breaking download
try {
    incrementDownloads($imageId);
} catch (Exception $e) {
    // Silently continue if increment fails
}

// Get file path
$filepath = IMAGES_DIR . $image['filename'];

if (!file_exists($filepath)) {
    header('HTTP/1.0 404 Not Found');
    echo "File not found";
    exit;
}

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . basename($image['filename']) . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Clear any active output buffers to avoid warnings when none exist
while (ob_get_level() > 0) {
    ob_end_clean();
}
flush();

// Read file and output
readfile($filepath);
exit;
