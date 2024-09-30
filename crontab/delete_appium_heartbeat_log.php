<?php
$yesterday = date('Y-m-d', strtotime('-1 day'));
$logDir = '/var/www/html/15.206.114.243/appium/logs/';
$pattern = $logDir . 'appium_heartbeat_' . $yesterday . '_*.log';
foreach (glob($pattern) as $logFile) {
  if (is_file($logFile)) {
    unlink($logFile);
    echo "Deleted: $logFile\n";
  } else {
    echo "File not found: $logFile\n";
  }
}
