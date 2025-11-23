<?php
// Khalti API keys configuration
// IMPORTANT: Set your keys here or via environment variables KHALTI_PUBLIC_KEY and KHALTI_SECRET_KEY.
// Use test keys while testing.
$envPk = getenv('KHALTI_PUBLIC_KEY');
$envSk = getenv('KHALTI_SECRET_KEY');
if ($envPk) {
    define('KHALTI_PUBLIC_KEY', $envPk);
} else {
    // TODO: replace with your own test/live key from Khalti dashboard
    define('KHALTI_PUBLIC_KEY', 'test_public_key_xxxxxxxxxxxxxxxxx');
}
if ($envSk) {
    define('KHALTI_SECRET_KEY', $envSk);
} else {
    // TODO: replace with your own test/live key from Khalti dashboard
    define('KHALTI_SECRET_KEY', 'test_secret_key_xxxxxxxxxxxxxxxxx');
}
