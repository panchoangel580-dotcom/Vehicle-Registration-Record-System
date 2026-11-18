<?php
/**
 * delete.php - DELETE
 * Accepts POST only (with CSRF token) so records can't be deleted by a plain link.
 */
require_once 'db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? '')) {
    set_flash('danger', 'Invalid request. The record was not deleted.');
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$stmt = run_query($conn, 'DELETE FROM vehicles WHERE vehicle_id = ?', 'i', [$id]);

if ($stmt->affected_rows > 0) {
    set_flash('success', 'The vehicle record was deleted.');
} else {
    set_flash('warning', 'That vehicle record no longer exists.');
}
header('Location: index.php');
exit;
