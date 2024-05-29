<?php
// Function to check if the request is coming from a bot
function isBot()
{
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
function ip_in_range($ip, $range)
{
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple CAPTCHA</title>
</head>

<body>
    <div id="captcha"></div>
    <script>
        // destination url after solving captcha
        const redirectTo = 'https://www.google.com';
        // Function to generate a cryptographically secure random number between min and max
        function getRandomNumber(min, max) {
            var byteArray = new Uint8Array(1);
            window.crypto.getRandomValues(byteArray);
            var range = max - min + 1;
            var maxRange = 256;
            if (byteArray[0] >= Math.floor(maxRange / range) * range)
                return getRandomNumber(min, max);
            return min + (byteArray[0] % range);
        }

        // Function to generate the CAPTCHA equation
        function generateCaptcha() {
            var num1 = getRandomNumber(1, 10);
            var num2 = getRandomNumber(1, 10);
            var operator = getRandomNumber(0, 1) ? '+' : '-';
            var equation = num1 + ' ' + operator + ' ' + num2;
            var result = operator === '+' ? num1 + num2 : num1 - num2;
            return { equation: equation, result: result };
        }

        // Function to display the CAPTCHA and handle user input
        function displayCaptcha() {
            var captcha = generateCaptcha();
            var userInput = prompt('Please solve the following CAPTCHA: ' + captcha.equation);
            if (userInput === null) {
                alert('CAPTCHA verification canceled.');
                return;
            }
            var userAnswer = parseInt(userInput);
            if (!isNaN(userAnswer) && userAnswer === captcha.result) {
                alert('CAPTCHA verification successful! Redirecting to Google...');
                window.location.href = 'https://www.google.com';
            } else {
                alert('CAPTCHA verification failed. Please try again.');
                displayCaptcha();
            }
        }

        // Call the function to display the CAPTCHA when the page loads
        window.onload = displayCaptcha;

    </script>
</body>

</html>