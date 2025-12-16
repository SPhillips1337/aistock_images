#!/bin/bash

# Manual trigger for stock photo automation
# Use this to test the automation without waiting for cron

echo "Running stock photo automation manually..."
echo "Check automation.log for detailed output"

/var/www/html/happymonkey.ai/stock/run_automation.sh

echo "Done! Check the logs:"
echo "  - automation.log (main log)"
echo "  - cleanup.log (cleanup operations)"
echo "  - index.log (indexing operations)"
