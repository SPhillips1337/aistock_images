<?php
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

// Increment download counter
incrementDownloads($imageId);

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

// Clear output buffer
ob_clean();
flush();

// Read file and output
readfile($filepath);
exit;
