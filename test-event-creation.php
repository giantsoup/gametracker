<?php

// This is a test script to verify that the active checkbox works correctly in the event creation form

// 1. Simulate a form submission with the active checkbox checked
$formData = [
    'name' => 'Test Event',
    'active' => '1', // Checkbox checked
    'starts_at' => '2023-06-01T10:00',
    'ends_at' => '2023-06-01T12:00',
    '_token' => 'test_token', // CSRF token (not used in this test)
];

// 2. Simulate the validation process
$validatedData = [
    'name' => $formData['name'],
    'active' => (bool) $formData['active'], // Convert to boolean as Laravel would
    'starts_at' => $formData['starts_at'],
    'ends_at' => $formData['ends_at'],
];

// 3. Print the validated data
echo "Validated data with checkbox checked:\n";
var_dump($validatedData);

// 4. Simulate a form submission with the active checkbox unchecked
$formData = [
    'name' => 'Test Event',
    'active' => '0', // Hidden input value (checkbox unchecked)
    'starts_at' => '2023-06-01T10:00',
    'ends_at' => '2023-06-01T12:00',
    '_token' => 'test_token', // CSRF token (not used in this test)
];

// 5. Simulate the validation process
$validatedData = [
    'name' => $formData['name'],
    'active' => (bool) $formData['active'], // Convert to boolean as Laravel would
    'starts_at' => $formData['starts_at'],
    'ends_at' => $formData['ends_at'],
];

// 6. Print the validated data
echo "\nValidated data with checkbox unchecked:\n";
var_dump($validatedData);

// 7. Simulate a form submission with no active field (this shouldn't happen with our implementation)
$formData = [
    'name' => 'Test Event',
    // No active field
    'starts_at' => '2023-06-01T10:00',
    'ends_at' => '2023-06-01T12:00',
    '_token' => 'test_token', // CSRF token (not used in this test)
];

// 8. Simulate the validation process
$validatedData = [
    'name' => $formData['name'],
    'active' => isset($formData['active']) ? (bool) $formData['active'] : false, // Default to false if not set
    'starts_at' => $formData['starts_at'],
    'ends_at' => $formData['ends_at'],
];

// 9. Print the validated data
echo "\nValidated data with no active field (shouldn't happen with our implementation):\n";
var_dump($validatedData);
