-- Stock Photo Website Database Schema

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    slug TEXT NOT NULL UNIQUE,
    thumbnail_path TEXT,
    image_count INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Images table
CREATE TABLE IF NOT EXISTS images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL UNIQUE,
    filepath TEXT NOT NULL,
    category_id INTEGER,
    prompt TEXT,
    seed INTEGER,
    model TEXT DEFAULT 'turbo',
    width INTEGER DEFAULT 1024,
    height INTEGER DEFAULT 1024,
    downloads INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Tags table for search functionality
CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    image_id INTEGER NOT NULL,
    tag_name TEXT NOT NULL,
    FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
);

-- Related images table for AI-generated suggestions
CREATE TABLE IF NOT EXISTS related_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    image_id INTEGER NOT NULL,
    related_image_id INTEGER NOT NULL,
    relevance_score REAL DEFAULT 0.5,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE,
    FOREIGN KEY (related_image_id) REFERENCES images(id) ON DELETE CASCADE,
    UNIQUE(image_id, related_image_id)
);

-- Create indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_images_category ON images(category_id);
CREATE INDEX IF NOT EXISTS idx_tags_image ON tags(image_id);
CREATE INDEX IF NOT EXISTS idx_tags_name ON tags(tag_name);
CREATE INDEX IF NOT EXISTS idx_images_created ON images(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_related_images ON related_images(image_id);
