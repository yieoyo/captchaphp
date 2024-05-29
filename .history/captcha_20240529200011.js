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
