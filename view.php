<?php
/**
 * view.php - READ (single record)
 */
require_once 'db_connect.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$r = run_query(
    $conn,
    'SELECT v.*, t.type_name FROM vehicles v JOIN vehicle_types t ON t.type_id = v.type_id WHERE v.vehicle_id = ?',
    'i', [$id]
)->get_result()->fetch_assoc();

if (!$r) {
    set_flash('danger', 'That vehicle record was not found.');
    header('Location: index.php');
    exit;
}

$pageTitle = $r['plate_number'];
$exp = expiry_state($r['registration_expiry']);
include 'includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="panel p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <span class="plate plate-lg"><?= e($r['plate_number']) ?></span>
                    <div class="h4 mt-3 mb-0"><?= e($r['brand'] . ' ' . $r['model']) ?></div>
                </div>
                <span class="badge fs-6 <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-md-4"><div class="detail-label">Type</div><?= e($r['type_name']) ?></div>
                <div class="col-sm-6 col-md-4"><div class="detail-label">Year model</div><?= e($r['year_model']) ?></div>
                <div class="col-sm-6 col-md-4"><div class="detail-label">Color</div><?= e($r['color']) ?></div>
                <div class="col-sm-6 col-md-4"><div class="detail-label">Owner</div><?= e($r['owner_name']) ?></div>
                <div class="col-sm-6 col-md-4"><div class="detail-label">Contact number</div><?= e($r['contact_number']) ?></div>
                <div class="col-sm-6 col-md-4"><div class="detail-label">Registration expiry</div>
                    <span class="<?= e($exp['class']) ?>"><?= e(date('M d, Y', strtotime($r['registration_expiry']))) ?> (<?= e($exp['label']) ?>)</span></div>
                <div class="col-12"><div class="detail-label">Remarks</div><?= $r['remarks'] ? nl2br(e($r['remarks'])) : '<span class="text-body-secondary">No remarks</span>' ?></div>
                <div class="col-sm-6"><div class="detail-label">Added</div><?= e(date('M d, Y g:i A', strtotime($r['created_at']))) ?></div>
                <div class="col-sm-6"><div class="detail-label">Last updated</div><?= e(date('M d, Y g:i A', strtotime($r['updated_at']))) ?></div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="edit.php?id=<?= (int)$r['vehicle_id'] ?>" class="btn btn-brand"><i class="bi bi-pencil me-1"></i>Edit vehicle</a>
                <a href="index.php" class="btn btn-outline-secondary">Back to list</a>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
