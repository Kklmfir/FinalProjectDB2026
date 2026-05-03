<?php
/**
 * Validation Functions
 */

/**
 * Validate required fields
 */
function validateRequired($fields, $data) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    return $errors;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate numeric
 */
function validateNumeric($value) {
    return is_numeric($value);
}

/**
 * Validate date
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate amount (positive number)
 */
function validateAmount($amount) {
    return is_numeric($amount) && $amount > 0;
}

/**
 * Sanitize and validate input
 */
function sanitizeAndValidate($data, $rules) {
    $errors = [];
    $sanitized = [];

    foreach ($rules as $field => $rule) {
        if (isset($data[$field])) {
            $value = sanitize($data[$field]);
            $sanitized[$field] = $value;

            if ($rule === 'required' && empty($value)) {
                $errors[] = ucfirst($field) . ' is required';
            } elseif ($rule === 'email' && !validateEmail($value)) {
                $errors[] = 'Invalid email format';
            } elseif ($rule === 'numeric' && !validateNumeric($value)) {
                $errors[] = ucfirst($field) . ' must be numeric';
            } elseif ($rule === 'amount' && !validateAmount($value)) {
                $errors[] = ucfirst($field) . ' must be a positive number';
            } elseif ($rule === 'date' && !validateDate($value)) {
                $errors[] = 'Invalid date format';
            }
        } elseif ($rule === 'required') {
            $errors[] = ucfirst($field) . ' is required';
        }
    }

    return ['errors' => $errors, 'data' => $sanitized];
}
?>