<?php
$db = new PDO('sqlite:' . __DIR__ . '/../stock_photos.db');
$stmt = $db->query("SELECT id, filename, created_at FROM images ORDER BY datetime(created_at) DESC LIMIT 20");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['created_at'] . "\t" . $r['id'] . "\t" . $r['filename'] . "\n";
}
