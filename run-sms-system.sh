#!/bin/bash

cd /opt/lampp/htdocs/included

echo "========================================="
echo "Starting SMS System with ZERO DELAY"
echo "========================================="

# Start Non-Masking Worker
echo "Starting Non-Masking Worker..."
sudo /opt/lampp/bin/php artisan queue:work redis --queue=non-masking --sleep=0 &
echo "✅ Non-Masking Worker Started (PID: $!)"

# Start Masking Worker
echo "Starting Masking Worker..."
sudo /opt/lampp/bin/php artisan queue:work redis --queue=masking --sleep=0 &
echo "✅ Masking Worker Started (PID: $!)"

# Start Continuous Dispatcher
echo "Starting Continuous Dispatcher..."
while true; do
    sudo /opt/lampp/bin/php artisan schedule:run
done &
echo "✅ Continuous Dispatcher Started (PID: $!)"

echo ""
echo "========================================="
echo "ALL SYSTEMS RUNNING WITH ZERO DELAY!"
echo "========================================="
echo ""
echo "Press Ctrl+C to stop all processes"
echo ""

# Wait for all processes
wait
