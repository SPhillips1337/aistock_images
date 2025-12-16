#!/bin/bash

# Stock Photo Website Setup Script

echo "==================================="
echo "Stock Photo Website Setup"
echo "==================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed"
    exit 1
fi

echo "✓ PHP is installed"

# Check if SQLite extension is available
php -m | grep -i sqlite > /dev/null
if [ $? -eq 0 ]; then
    echo "✓ SQLite extension is available"
else
    echo "⚠ Warning: SQLite extension may not be properly configured"
fi

# Create symlink for images
echo ""
echo "Creating symlink for images directory..."
cd "$(dirname "$0")"
ln -sf "$(pwd)/images" public/images
echo "✓ Symlink created"

# Index images
echo ""
echo "Indexing images in database..."
php scripts/index_images.php

echo ""
echo "==================================="
echo "Setup Complete!"
echo "==================================="
echo ""
echo "To start the development server, run:"
echo "  cd public && php -S localhost:8000"
echo ""
echo "Then open your browser to:"
echo "  http://localhost:8000"
echo ""
