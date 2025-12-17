<?php
/**
 * SEO Enhancement Functions for Stock Photo Website
 * Includes structured data schema and meta tags
 */

/**
 * Generate JSON-LD structured data schema
 */
function generate_schema_data($page_type, $page_data = []) {
    $base_url = defined('SITE_URL') ? SITE_URL : 'http://localhost';
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'AI Stock Photos';
    
    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => []
    ];
    
    switch($page_type) {
        case 'home':
            $schema['@graph'][] = [
                '@type' => 'WebSite',
                '@id' => $base_url . '/',
                'url' => $base_url . '/',
                'name' => $site_name,
                'description' => 'Browse thousands of high-quality AI-generated stock photos across various categories. Free images for commercial and personal use.',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $base_url . '/search?q={search_term}',
                    'query-input' => 'required name=search_term'
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $site_name,
                    'url' => $base_url,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $base_url . '/assets/images/logo.png',
                        'width' => 512,
                        'height' => 512
                    ]
                ]
            ];
            
            $schema['@graph'][] = [
                '@type' => 'ImageGallery',
                'name' => $site_name . ' Gallery',
                'description' => 'High-quality AI-generated stock photos organized by category',
                'url' => $base_url . '/#categories'
            ];
            break;
            
        case 'category':
            $category = $page_data['category'] ?? null;
            $images = $page_data['images'] ?? [];
            
            if ($category) {
                $item_list_elements = [];
                foreach ($images as $image) {
                    $item_list_elements[] = [
                        '@type' => 'ImageObject',
                        'url' => $base_url . '/image.php?id=' . $image['id'],
                        'name' => $image['category_name'] . ' - AI Stock Photo ' . $image['id'],
                        'description' => 'High-quality AI-generated ' . $image['category_name'] . ' stock photo',
                        'contentUrl' => $base_url . '/download.php?id=' . $image['id'],
                        'thumbnailUrl' => $base_url . '/thumbnail.php?w=300&h=225&img=/images/' . $image['filename'],
                        'width' => 1024,
                        'height' => 1024,
                        'author' => [
                            '@type' => 'Organization',
                            'name' => $site_name
                        ],
                        'publisher' => [
                            '@type' => 'Organization', 
                            'name' => $site_name
                        ],
                        'license' => 'https://creativecommons.org/licenses/by/4.0/',
                        'dateModified' => $image['created_at'],
                        'interactionStatistic' => [
                            '@type' => 'InteractionCounter',
                            'interactionType' => 'https://schema.org/DownloadAction',
                            'userInteractionCount' => $image['downloads']
                        ]
                    ];
                }
                
                $schema['@graph'][] = [
                    '@type' => 'CollectionPage',
                    'url' => $base_url . '/category.php?slug=' . $category['slug'],
                    'name' => $category['name'] . ' AI Stock Photos',
                    'description' => 'Browse ' . $category['actual_count'] . ' AI-generated images in the ' . $category['name'] . ' category. High-quality stock photos for commercial use.',
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => $site_name,
                        'url' => $base_url
                    ],
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $category['actual_count'],
                        'itemListElement' => $item_list_elements
                    ],
                    'breadcrumb' => [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            [
                                '@type' => 'ListItem',
                                'position' => 1,
                                'name' => 'Home',
                                'item' => $base_url . '/'
                            ],
                            [
                                '@type' => 'ListItem',
                                'position' => 2,
                                'name' => $category['name'],
                                'item' => $base_url . '/category.php?slug=' . $category['slug']
                            ]
                        ]
                    ]
                ];
            }
            break;
            
        case 'search':
            $search_query = $page_data['query'] ?? '';
            $images = $page_data['results'] ?? [];
            
            $item_list_elements = [];
            foreach ($images as $image) {
                $item_list_elements[] = [
                    '@type' => 'ImageObject',
                    'url' => $base_url . '/image.php?id=' . $image['id'],
                    'name' => $image['category_name'] . ' - AI Stock Photo',
                    'description' => 'AI-generated stock photo matching: ' . $search_query,
                    'thumbnailUrl' => $base_url . '/thumbnail.php?w=300&h=225&img=/images/' . $image['filename']
                ];
            }
            
            $schema['@graph'][] = [
                '@type' => 'SearchResultsPage',
                'url' => $base_url . '/search.php?q=' . urlencode($search_query),
                'name' => 'Search Results for: ' . $search_query,
                'description' => 'Find AI-generated stock photos matching: ' . $search_query,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($images),
                    'itemListElement' => $item_list_elements
                ]
            ];
            break;
            
        case 'image':
            $image = $page_data['image'] ?? null;
            if ($image) {
                $schema['@graph'][] = [
                    '@type' => 'ImageObject',
                    'url' => $base_url . '/image.php?id=' . $image['id'],
                    'name' => $image['category_name'] . ' - AI Stock Photo ' . $image['id'],
                    'description' => 'High-quality AI-generated ' . $image['category_name'] . ' stock photo. Free download for commercial use.',
                    'contentUrl' => $base_url . '/download.php?id=' . $image['id'],
                    'thumbnailUrl' => $base_url . '/thumbnail.php?w=300&h=225&img=/images/' . $image['filename'],
                    'image' => $base_url . '/images/' . $image['filename'],
                    'width' => 1024,
                    'height' => 1024,
                    'author' => [
                        '@type' => 'Organization',
                        'name' => $site_name
                    ],
                    'creator' => [
                        '@type' => 'Organization',
                        'name' => $site_name
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $site_name,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $base_url . '/assets/images/logo.png',
                            'width' => 512,
                            'height' => 512
                        ]
                    ],
                    'license' => 'https://creativecommons.org/licenses/by/4.0/',
                    'acquireLicensePage' => $base_url . '/terms-of-service.php',
                    'dateCreated' => $image['created_at'],
                    'dateModified' => $image['created_at'],
                    'uploadDate' => $image['created_at'],
                    'interactionStatistic' => [
                        '@type' => 'InteractionCounter',
                        'interactionType' => 'https://schema.org/DownloadAction',
                        'userInteractionCount' => $image['downloads']
                    ],
                    'breadcrumb' => [
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            [
                                '@type' => 'ListItem',
                                'position' => 1,
                                'name' => 'Home',
                                'item' => $base_url . '/'
                            ],
                            [
                                '@type' => 'ListItem',
                                'position' => 2,
                                'name' => 'Categories',
                                'item' => $base_url . '/#categories'
                            ],
                            [
                                '@type' => 'ListItem',
                                'position' => 3,
                                'name' => $image['category_name'],
                                'item' => $base_url . '/category.php?slug=' . $image['category_slug']
                            ]
                        ]
                    ]
                ];
            }
            break;
    }
    
    return $schema;
}

/**
 * Generate dynamic meta tags for SEO
 */
function generate_meta_tags($page_type, $page_data = []) {
    $base_url = defined('SITE_URL') ? SITE_URL : 'http://localhost';
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'AI Stock Photos';
    
    $tags = '';
    
    switch($page_type) {
        case 'home':
            $tags .= '
            <meta name="robots" content="index, follow, max-image-preview:large">
            <meta name="google-site-verification" content="your-google-verification-code">
            <meta name="msvalidate.01" content="your-bing-verification-code">
            <meta property="og:locale" content="en_US">
            <meta property="og:site_name" content="' . $site_name . '">
            <meta property="og:type" content="website">
            <meta property="og:title" content="AI Stock Photos - High-Quality AI-Generated Images">
            <meta property="og:description" content="Browse thousands of high-quality AI-generated stock photos across various categories. Free images for commercial and personal use.">
            <meta property="og:url" content="' . $base_url . '/">
            <meta property="og:image" content="' . $base_url . '/assets/images/og-image.jpg">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="AI Stock Photos - High-Quality AI-Generated Images">
            <meta name="twitter:description" content="Browse thousands of high-quality AI-generated stock photos across various categories. Free images for commercial and personal use.">
            <meta name="twitter:image" content="' . $base_url . '/assets/images/og-image.jpg">
            <meta name="keywords" content="AI stock photos, artificial intelligence images, free stock photography, commercial images, AI generated, machine learning photos">
            <link rel="canonical" href="' . $base_url . '/">
            ';
            break;
            
        case 'category':
            $category = $page_data['category'] ?? null;
            if ($category) {
                $tags .= '
            <meta name="robots" content="index, follow, max-image-preview:large">
            <meta property="og:locale" content="en_US">
            <meta property="og:site_name" content="' . $site_name . '">
            <meta property="og:type" content="website">
            <meta property="og:title" content="' . htmlspecialchars($category['name']) . ' AI Stock Photos - High-Quality Images">
            <meta property="og:description" content="Browse ' . $category['actual_count'] . ' AI-generated images in the ' . htmlspecialchars($category['name']) . ' category. High-quality stock photos for commercial use.">
            <meta property="og:url" content="' . $base_url . '/category.php?slug=' . $category['slug'] . '">
            <meta property="og:image" content="' . $base_url . ($category['thumbnail_path'] ? '/images/' . $category['thumbnail_path'] : '/assets/images/og-category.jpg') . '">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="' . htmlspecialchars($category['name']) . ' AI Stock Photos">
            <meta name="twitter:description" content="Browse ' . $category['actual_count'] . ' AI-generated images in the ' . htmlspecialchars($category['name']) . ' category">
            <meta name="keywords" content="' . htmlspecialchars($category['name']) . ' AI photos, ' . htmlspecialchars($category['name']) . ' stock photography, AI generated ' . htmlspecialchars($category['name']) . ' images">
            <link rel="canonical" href="' . $base_url . '/category.php?slug=' . $category['slug'] . '">
            ';
            }
            break;
            
        case 'search':
            $search_query = $page_data['query'] ?? '';
            if ($search_query) {
                $tags .= '
            <meta name="robots" content="index, follow, max-image-preview:large">
            <meta property="og:locale" content="en_US">
            <meta property="og:site_name" content="' . $site_name . '">
            <meta property="og:type" content="website">
            <meta property="og:title" content="Search Results for: ' . htmlspecialchars($search_query) . ' - ' . $site_name . '">
            <meta property="og:description" content="Find AI-generated stock photos matching: ' . htmlspecialchars($search_query) . '">
            <meta property="og:url" content="' . $base_url . '/search.php?q=' . urlencode($search_query) . '">
            <meta name="twitter:card" content="summary">
            <meta name="twitter:title" content="Search Results for: ' . htmlspecialchars($search_query) . ' - ' . $site_name . '">
            <meta name="twitter:description" content="Find AI-generated stock photos matching: ' . htmlspecialchars($search_query) . '">
            <meta name="keywords" content="' . htmlspecialchars($search_query) . ', AI stock photos, AI generated images, artificial intelligence photography">
            <meta name="robots" content="noindex, follow">
            ';
            }
            break;
            
        case 'image':
            $image = $page_data['image'] ?? null;
            if ($image) {
                $tags .= '
            <meta name="robots" content="index, follow, max-image-preview:large">
            <meta property="og:locale" content="en_US">
            <meta property="og:site_name" content="' . $site_name . '">
            <meta property="og:type" content="article">
            <meta property="og:title" content="' . htmlspecialchars($image['category_name']) . ' - AI Stock Photo ' . $image['id'] . '">
            <meta property="og:description" content="High-quality AI-generated ' . htmlspecialchars($image['category_name']) . ' stock photo. Free download for commercial use.">
            <meta property="og:url" content="' . $base_url . '/image.php?id=' . $image['id'] . '">
            <meta property="og:image" content="' . $base_url . '/images/' . $image['filename'] . '">
            <meta property="og:image:width" content="1024">
            <meta property="og:image:height" content="1024">
            <meta property="og:image:alt" content="' . htmlspecialchars($image['category_name']) . ' AI stock photo">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="' . htmlspecialchars($image['category_name']) . ' - AI Stock Photo ' . $image['id'] . '">
            <meta name="twitter:description" content="High-quality AI-generated ' . htmlspecialchars($image['category_name']) . ' stock photo. Free download.">
            <meta name="twitter:image" content="' . $base_url . '/images/' . $image['filename'] . '">
            <meta name="keywords" content="' . htmlspecialchars($image['category_name']) . ', AI photos, stock photography, AI generated, commercial images">
            <meta property="article:section" content="' . htmlspecialchars($image['category_name']) . '">
            <meta property="article:tag" content="' . htmlspecialchars($image['category_name']) . '">
            <link rel="canonical" href="' . $base_url . '/image.php?id=' . $image['id'] . '">
            ';
            }
            break;
    }
    
    return $tags;
}

/**
 * Output schema and meta tags to HTML head
 */
function output_seo_head($page_type, $page_data = []) {
    // Generate schema JSON-LD
    $schema = generate_schema_data($page_type, $page_data);
    if (!empty($schema)) {
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
    
    // Generate meta tags
    $meta_tags = generate_meta_tags($page_type, $page_data);
    echo $meta_tags;
}

?>