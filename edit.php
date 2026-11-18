<?php
/**
 * edit.php - UPDATE
 */
require_once 'db_connect.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$row = run_query($conn, 'SELECT * FROM vehicles WHERE vehicle_id = ?', 'i', [$id])->get_result()->fetch_assoc();

if (!$row) {
    set_flash('danger', 'That vehicle record was not found.');
    header('Location: index.php');
    exit;
}

$pageTitle = 'Edit vehicle';
$types  = get_vehicle_types($conn);
$typeIds = array_map('intval', array_column($types, 'type_id'));
$errors = [];
$v = [
    'plate_number' => $row['plate_number'], 'type_id' => $row['type_id'], 'brand' => $row['brand'],
    'model' => $row['model'], 'year_model' => $row['year_model'], 'color' => $row['color'],
    'owner_name' => $row['owner_name'], 'contact_number' => $row['contact_number'],
    'registration_expiry' => $row['registration_expiry'], 'status' => $row['status'],
    'remarks' => (string)$row['remarks'],
];

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
                    'UPDATE vehicles
                     SET plate_number = ?, type_id = ?, brand = ?, model = ?, year_model = ?, color = ?,
                         owner_name = ?, contact_number = ?, registration_expiry = ?, status = ?, remarks = ?
                     WHERE vehicle_id = ?',
                    'sississssssi',
                    [$v['plate_number'], (int)$v['type_id'], $v['brand'], $v['model'], (int)$v['year_model'],
                     $v['color'], $v['owner_name'], $v['contact_number'], $v['registration_expiry'],
                     $v['status'], $v['remarks'] !== '' ? $v['remarks'] : null, $id]
                );
                set_flash('success', 'Vehicle ' . $v['plate_number'] . ' was updated.');
                header('Location: index.php');
                exit;
            } catch (mysqli_sql_exception $ex) {
                if ($ex->getCode() === 1062) {
                    $errors['plate_number'] = 'Another vehicle already uses this plate number.';
                } else {
                    $errors['form'] = 'Could not update the record. Please try again.';
                }
            }
        }
    }
}

$submitLabel = 'Save changes';
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <h1 class="h2 mb-3">Edit vehicle</h1>
        <div class="panel p-4">
            <?php if (isset($errors['form'])): ?>
                <div class="alert alert-danger"><?= e($errors['form']) ?></div>
            <?php endif; ?>
            <?php include 'includes/form.php'; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
