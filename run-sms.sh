#!/bin/bash

# Navigate to the project directory
cd /opt/lampp/htdocs/included

# Display some information
echo "========================================"
echo "Single Worker - No Conflicts"
echo "Press Ctrl+C to stop"
echo "========================================"
echo

# Infinite loop to process the job
while true; do
    php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); (new App\Jobs\ProcessSmsCampaignJob())->handle();"
done
