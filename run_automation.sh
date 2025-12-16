#!/bin/bash

# Stock Photo Automation Script
# Handles image generation, database updates, and cleanup

PROJECT_DIR="/var/www/html/happymonkey.ai/stock"
LOG_FILE="$PROJECT_DIR/automation.log"

cd "$PROJECT_DIR" || exit 1

log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
    echo "$1"
}

log_message "========================================="
log_message "Starting Stock Photo Automation"

# 1. Clean up deleted images first
log_message "Step 1: Cleaning up deleted images..."
php scripts/cleanup_deleted.php >> "$LOG_FILE" 2>&1

# 2. Generate new images
log_message "Step 2: Generating new images..."
./venv/bin/python3 auto_stock_creator.py >> "$LOG_FILE" 2>&1

# 3. Index new images
log_message "Step 3: Indexing new images..."
php scripts/index_images.php >> "$LOG_FILE" 2>&1

log_message "Automation complete!"
log_message "========================================="
