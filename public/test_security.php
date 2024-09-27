<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/User.php';

function testCSRF() {
    echo "<h2>Testing CSRF Protection</h2>";
    
    // Generate a valid token
    $validToken = generateCSRFToken();
    echo "Valid Token: $validToken<br>";
    
    // Test with valid token
    echo "Testing with valid token: " . (validateCSRFToken($validToken) ? "Passed" : "Failed") . "<br>";
    
    // Test with invalid token
    $invalidToken = "invalid_token";
    echo "Testing with invalid token: " . (!validateCSRFToken($invalidToken) ? "Passed" : "Failed") . "<br>";
}

function testRateLimiting() {
    echo "<h2>Testing Rate Limiting</h2>";
    
    $testUser = "testuser";
    
    echo "Simulating 6 login attempts:<br>";
    for ($i = 1; $i <= 6; $i++) {
        if (checkRateLimit($testUser)) {
            echo "Attempt $i: Allowed<br>";
            incrementLoginAttempts($testUser);
        } else {
            echo "Attempt $i: Blocked (Rate limit exceeded)<br>";
        }
    }
    
    echo "<br>Waiting 2 seconds...<br>";
    sleep(2);
    echo "Attempting login after short wait: " . (checkRateLimit($testUser) ? "Allowed" : "Blocked") . "<br>";
    
    echo "<br>Resetting login attempts...<br>";
    resetLoginAttempts($testUser);
    echo "Attempting login after reset: " . (checkRateLimit($testUser) ? "Allowed" : "Blocked") . "<br>";
}

// Run tests
testCSRF();
echo "<hr>";
testRateLimiting();
?>