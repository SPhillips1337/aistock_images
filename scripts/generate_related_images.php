#!/usr/bin/env php
<?php
/**
 * Generate Related Images Script
 * Uses Ollama LLM to analyze images and create related image suggestions
 */

require_once __DIR__ . '/../includes/config.php';

echo "Generating related image suggestions...\n";

$db = getDB();

// Get all images that don't have related suggestions yet
$stmt = $db->query("
    SELECT i.*, c.name as category_name 
    FROM images i 
    LEFT JOIN categories c ON i.category_id = c.id 
    LEFT JOIN related_images r ON i.id = r.image_id 
    WHERE r.image_id IS NULL
    ORDER BY i.created_at DESC
");
$images = $stmt->fetchAll();

$processed = 0;

foreach ($images as $image) {
    echo "Processing: {$image['filename']}\n";
    
    // Generate related image suggestions using Ollama
    $relatedIds = generateRelatedSuggestions($image);
    
    if (!empty($relatedIds)) {
        // Store related image suggestions
        foreach ($relatedIds as $relatedId) {
            $insertStmt = $db->prepare("
                INSERT OR IGNORE INTO related_images (image_id, related_image_id, relevance_score) 
                VALUES (?, ?, ?)
            ");
            $insertStmt->execute([$image['id'], $relatedId, 0.8]);
        }
        echo "  Added " . count($relatedIds) . " related images\n";
    }
    
    $processed++;
    
    // Rate limit to avoid overwhelming the API
    if ($processed % 10 == 0) {
        sleep(2);
    }
}

echo "Related image generation complete! Processed: $processed images\n";

function generateRelatedSuggestions($image) {
    global $db;
    
    // Create prompt for Ollama
    $prompt = "Based on this image description: '{$image['category_name']} - {$image['prompt']}', suggest 5 related image categories or themes that would be complementary for a stock photo website. Return only category names, one per line, no explanations.";
    
    $payload = [
        'model' => $_ENV['OLLAMA_TEXT_MODEL'] ?? 'llama2',
        'prompt' => $prompt,
        'stream' => false
    ];
    
    try {
        $ch = curl_init($_ENV['OLLAMA_URL'] ?? 'http://localhost:11434/api/generate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            return [];
        }
        
        $result = json_decode($response, true);
        $suggestions = explode("\n", trim($result['response'] ?? ''));
        
        // Find matching images for each suggestion
        $relatedIds = [];
        foreach ($suggestions as $suggestion) {
            $suggestion = trim($suggestion);
            if (empty($suggestion)) continue;
            
            $searchStmt = $db->prepare("
                SELECT i.id FROM images i 
                LEFT JOIN categories c ON i.category_id = c.id 
                WHERE (c.name LIKE ? OR i.prompt LIKE ?) 
                AND i.id != ? 
                LIMIT 2
            ");
            $searchTerm = '%' . $suggestion . '%';
            $searchStmt->execute([$searchTerm, $searchTerm, $image['id']]);
            
            while ($row = $searchStmt->fetch()) {
                $relatedIds[] = $row['id'];
            }
        }
        
        return array_unique(array_slice($relatedIds, 0, 6));
        
    } catch (Exception $e) {
        echo "  Error generating suggestions: " . $e->getMessage() . "\n";
        return [];
    }
}
?>
