<?php
function validateInput($data) {
    if (empty($data['username'])) {
        return 'Username is required';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email';
    }
    return 'OK';
}
