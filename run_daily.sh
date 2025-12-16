#!/bin/bash

# Navigate to the project directory
cd /var/www/html/happymonkey.ai/stock || exit 1

# Log the start time
echo "----------------------------------------" >> daily_run.log
echo "Starting Stock Photo Generation: $(date)" >> daily_run.log

# Run the script using the virtual environment python
./venv/bin/python3 auto_stock_creator.py >> daily_run.log 2>&1

# Update database with new images
php scripts/index_images.php >> daily_run.log 2>&1

# Log completion
echo "Completed: $(date)" >> daily_run.log
echo "----------------------------------------" >> daily_run.log
