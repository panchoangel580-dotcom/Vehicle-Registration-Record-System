<?php
/**
 * create.php - CREATE (INSERT)
 */
require_once 'db_connect.php';
require_once 'includes/functions.php';

$pageTitle = 'Add vehicle';
$types  = get_vehicle_types($conn);
$typeIds = array_map('intval', array_column($types, 'type_id'));
$errors = [];
$v = ['plate_number' => '', 'type_id' => '', 'brand' => '', 'model' => '', 'year_model' => '',
      'color' => '', 'owner_name' => '', 'contact_number' => '', 'registration_expiry' => '',
      'status' => 'Active', 'remarks' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors['form'] = 'Your session expired. Reload the page and try again.';
    } else {
        $v = collect_vehicle_input($_POST);
        $errors = validate_vehicle($v, $typeIds);

        if (!$errors) {
            try {
                run_query(
                    $conn,
                    'INSERT INTO vehicles (plate_number, type_id, brand, model, year_model, color, owner_name,
                                           contact_number, registration_expiry, status, remarks)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    'sississssss',
                    [$v['plate_number'], (int)$v['type_id'], $v['brand'], $v['model'], (int)$v['year_model'],
                     $v['color'], $v['owner_name'], $v['contact_number'], $v['registration_expiry'],
                     $v['status'], $v['remarks'] !== '' ? $v['remarks'] : null]
                );
                set_flash('success', 'Vehicle ' . $v['plate_number'] . ' was added.');
                header('Location: index.php');
                exit;
            } catch (mysqli_sql_exception $ex) {
                if ($ex->getCode() === 1062) {
                    $errors['plate_number'] = 'This plate number is already registered.';
                } else {
                    $errors['form'] = 'Could not save the record. Please try again.';
                }
            }
        }
    }
}

$submitLabel = 'Save vehicle';
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h2 mb-3">Add a vehicle</h1>
        <div class="panel p-4">
            <?php if (isset($errors['form'])): ?>
                <div class="alert alert-danger"><?= e($errors['form']) ?></div>
            <?php endif; ?>
            <?php include 'includes/form.php'; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
