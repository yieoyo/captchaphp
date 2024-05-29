<?php
// Function to check if the request is coming from a bot
function isBot() {
    // Check if the request has a user agent header
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        // Check if the user agent contains typical bot keywords
        $botKeywords = array('bot', 'crawl', 'spider', 'google', 'bing', 'yahoo');
        foreach ($botKeywords as $keyword) {
            if (strpos($userAgent, $keyword) !== false) {
                return true;
            }
        }
    }

    // Check if the request is coming from a known bot IP address range
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $botIPRanges = array(
        '1.1.1.1', // Example IP range
        // Add more IP ranges as needed
    );
    foreach ($botIPRanges as $range) {
        if (ip_in_range($clientIP, $range)) {
            return true;
        }
    }

    // Add more checks here as needed

    return false; // Default to false (not a bot)
}

// Function to check if an IP address is within a specified range
function ip_in_range($ip, $range) {
    if (strpos($range, '/') === false) {
        $range .= '/32';
    }
    list($subnet, $bits) = explode('/', $range, 2);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    return ($ip & $mask) == $subnet;
}

// Check if the request is from a bot
if (isBot()) {
    http_response_code(403); // Forbidden
    echo "Access forbidden."; // Display an error message
    exit; // Stop further execution
}

// Serve the index.html page to legitimate users
include 'index.html';
?>
