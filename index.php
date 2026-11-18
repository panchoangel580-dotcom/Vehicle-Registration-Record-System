<?php
/**
 * index.php - READ (SELECT)
 * Lists vehicles with search, filters, summary counts and pagination.
 */
require_once 'db_connect.php';
require_once 'includes/functions.php';

$pageTitle = 'All vehicles';
$perPage   = 8;

// ---- Filters ----
$q      = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$typeId = (int)($_GET['type'] ?? 0);
$page   = max(1, (int)($_GET['page'] ?? 1));

if (!in_array($status, STATUS_OPTIONS, true)) $status = '';

$where  = [];
$params = [];
$bind   = '';

if ($q !== '') {
    $where[] = '(v.plate_number LIKE ? OR v.brand LIKE ? OR v.model LIKE ? OR v.owner_name LIKE ?)';
    $like = "%$q%";
    array_push($params, $like, $like, $like, $like);
    $bind .= 'ssss';
}
if ($status !== '') {
    $where[] = 'v.status = ?';
    $params[] = $status;
    $bind .= 's';
}
if ($typeId > 0) {
    $where[] = 'v.type_id = ?';
    $params[] = $typeId;
    $bind .= 'i';
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ---- Summary counts ----
$stats = $conn->query(
    "SELECT COUNT(*) AS total,
            SUM(status = 'Active') AS active,
            SUM(status = 'Active' AND registration_expiry >= CURDATE() AND registration_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) AS expiring,
            SUM(status = 'Active' AND registration_expiry < CURDATE()) AS expired
     FROM vehicles"
)->fetch_assoc();

// ---- Count for pagination ----
$total = (int)run_query($conn, "SELECT COUNT(*) FROM vehicles v $whereSql", $bind, $params)
            ->get_result()->fetch_row()[0];
$pages = max(1, (int)ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

// ---- Fetch rows ----
$sql = "SELECT v.*, t.type_name
        FROM vehicles v
        JOIN vehicle_types t ON t.type_id = v.type_id
        $whereSql
        ORDER BY v.vehicle_id DESC
        LIMIT ? OFFSET ?";
$rows = run_query($conn, $sql, $bind . 'ii', array_merge($params, [$perPage, $offset]))
            ->get_result()->fetch_all(MYSQLI_ASSOC);

$types = get_vehicle_types($conn);

function page_url(int $p): string
{
    return 'index.php?' . http_build_query(array_merge($_GET, ['page' => $p]));
}

include 'includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h2 mb-0">Vehicle records</h1>
    <a href="create.php" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Add vehicle</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat"><div class="num"><?= (int)$stats['total'] ?></div>Total vehicles</div></div>
    <div class="col-6 col-lg-3"><div class="stat gray"><div class="num"><?= (int)$stats['active'] ?></div>Active</div></div>
    <div class="col-6 col-lg-3"><div class="stat warn"><div class="num"><?= (int)$stats['expiring'] ?></div>Expiring in 30 days</div></div>
    <div class="col-6 col-lg-3"><div class="stat bad"><div class="num"><?= (int)$stats['expired'] ?></div>Registration expired</div></div>
</div>

<form class="panel p-3 mb-3" method="get">
    <div class="row g-2">
        <div class="col-md-5">
            <input type="search" name="q" class="form-control" placeholder="Search plate, brand, model or owner" value="<?= e($q) ?>" aria-label="Search">
        </div>
        <div class="col-6 col-md-2">
            <select name="type" class="form-select" aria-label="Filter by type">
                <option value="0">All types</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t['type_id'] ?>" <?= $typeId === (int)$t['type_id'] ? 'selected' : '' ?>><?= e($t['type_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select" aria-label="Filter by status">
                <option value="">All statuses</option>
                <?php foreach (STATUS_OPTIONS as $s): ?>
                    <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-dark flex-fill" type="submit"><i class="bi bi-search me-1"></i>Search</button>
            <a href="index.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="panel">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Plate</th>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Owner</th>
                    <th>Registration</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <p class="mb-2">No vehicles match your search.</p>
                        <a href="create.php" class="btn btn-sm btn-brand">Add a vehicle</a>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($rows as $r): $exp = expiry_state($r['registration_expiry']); ?>
                <tr>
                    <td><span class="plate"><?= e($r['plate_number']) ?></span></td>
                    <td>
                        <div class="fw-semibold"><?= e($r['brand'] . ' ' . $r['model']) ?></div>
                        <div class="small text-body-secondary"><?= e($r['year_model']) ?> &middot; <?= e($r['color']) ?></div>
                    </td>
                    <td><?= e($r['type_name']) ?></td>
                    <td>
                        <div><?= e($r['owner_name']) ?></div>
                        <div class="small text-body-secondary"><?= e($r['contact_number']) ?></div>
                    </td>
                    <td class="<?= e($exp['class']) ?>"><?= e($exp['label']) ?></td>
                    <td><span class="badge <?= status_class($r['status']) ?>"><?= e($r['status']) ?></span></td>
                    <td class="text-end text-nowrap">
                        <a href="view.php?id=<?= (int)$r['vehicle_id'] ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="edit.php?id=<?= (int)$r['vehicle_id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"
                                data-id="<?= (int)$r['vehicle_id'] ?>" data-plate="<?= e($r['plate_number']) ?>"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top gap-2">
        <span class="small text-body-secondary">
            <?= $total ? 'Showing ' . ($offset + 1) . ' to ' . min($offset + $perPage, $total) . " of $total" : 'No results' ?>
        </span>
        <?php if ($pages > 1): ?>
        <nav aria-label="Pagination">
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(page_url($page - 1)) ?>">Previous</a></li>
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= e(page_url($i)) ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(page_url($page + 1)) ?>">Next</a></li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="delete.php">
            <div class="modal-header">
                <h2 class="modal-title h5" id="deleteTitle">Delete vehicle record</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Delete the record for <span class="plate" id="deletePlate"></span>? This cannot be undone.
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" id="deleteId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep record</button>
                <button type="submit" class="btn btn-danger">Delete record</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.btn-delete').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('deleteId').value = btn.dataset.id;
        document.getElementById('deletePlate').textContent = btn.dataset.plate;
    });
});
</script>

<?php include 'includes/footer.php'; ?>
