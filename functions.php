<?php
/**
 * includes/functions.php
 * Shared helpers: escaping, flash messages, CSRF, validation, prepared queries.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const STATUS_OPTIONS = ['Active', 'Inactive', 'Under Repair', 'Sold'];

/** Escape output to prevent XSS */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Store a one-time message (success / danger / warning) */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Read and clear the flash message */
function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/** CSRF protection */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check($token): bool
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

/** Run a prepared statement and return the executed statement */
function run_query(mysqli $conn, string $sql, string $types = '', array $params = []): mysqli_stmt
{
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

/** Fetch all vehicle types for dropdowns */
function get_vehicle_types(mysqli $conn): array
{
    return $conn->query('SELECT type_id, type_name FROM vehicle_types ORDER BY type_name')->fetch_all(MYSQLI_ASSOC);
}

/** Pull and trim form input into a clean array */
function collect_vehicle_input(array $src): array
{
    $fields = ['plate_number', 'type_id', 'brand', 'model', 'year_model', 'color',
               'owner_name', 'contact_number', 'registration_expiry', 'status', 'remarks'];
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = trim((string)($src[$f] ?? ''));
    }
    $data['plate_number'] = strtoupper($data['plate_number']);
    return $data;
}

/** Validate vehicle form data. Returns [field => message] */
function validate_vehicle(array $d, array $validTypeIds): array
{
    $errors = [];
    $maxYear = (int)date('Y') + 1;

    if ($d['plate_number'] === '' || !preg_match('/^[A-Z0-9 -]{5,15}$/', $d['plate_number'])) {
        $errors['plate_number'] = 'Enter a plate number of 5 to 15 letters, numbers, spaces or dashes.';
    }
    if (!in_array((int)$d['type_id'], $validTypeIds, true)) {
        $errors['type_id'] = 'Choose a vehicle type.';
    }
    if ($d['brand'] === '' || mb_strlen($d['brand']) > 50) {
        $errors['brand'] = 'Enter the brand (up to 50 characters).';
    }
    if ($d['model'] === '' || mb_strlen($d['model']) > 50) {
        $errors['model'] = 'Enter the model (up to 50 characters).';
    }
    if (!ctype_digit($d['year_model']) || (int)$d['year_model'] < 1950 || (int)$d['year_model'] > $maxYear) {
        $errors['year_model'] = "Enter a year between 1950 and $maxYear.";
    }
    if ($d['color'] === '' || mb_strlen($d['color']) > 30) {
        $errors['color'] = 'Enter the color (up to 30 characters).';
    }
    if ($d['owner_name'] === '' || mb_strlen($d['owner_name']) > 100) {
        $errors['owner_name'] = 'Enter the owner\'s full name.';
    }
    if (!preg_match('/^[0-9+() -]{7,20}$/', $d['contact_number'])) {
        $errors['contact_number'] = 'Enter a valid contact number (7 to 20 digits).';
    }
    $date = DateTime::createFromFormat('Y-m-d', $d['registration_expiry']);
    if (!$date || $date->format('Y-m-d') !== $d['registration_expiry']) {
        $errors['registration_expiry'] = 'Choose the registration expiry date.';
    }
    if (!in_array($d['status'], STATUS_OPTIONS, true)) {
        $errors['status'] = 'Choose a status.';
    }
    if (mb_strlen($d['remarks']) > 1000) {
        $errors['remarks'] = 'Remarks can be up to 1000 characters.';
    }
    return $errors;
}

/** Bootstrap badge class for a status */
function status_class(string $status): string
{
    switch ($status) {
        case 'Active':       return 'text-bg-success';
        case 'Under Repair': return 'text-bg-warning';
        case 'Sold':         return 'text-bg-secondary';
        default:             return 'text-bg-danger';
    }
}

/** Registration state: ['label' => ..., 'class' => ...] */
function expiry_state(string $date): array
{
    $today = new DateTime('today');
    $exp   = new DateTime($date);
    $days  = (int)$today->diff($exp)->format('%r%a');

    if ($days < 0)   return ['label' => 'Expired ' . abs($days) . 'd ago', 'class' => 'text-danger fw-semibold'];
    if ($days <= 30) return ['label' => "Expires in {$days}d",             'class' => 'text-warning-emphasis fw-semibold'];
    return ['label' => date('M d, Y', strtotime($date)), 'class' => 'text-body'];
}
