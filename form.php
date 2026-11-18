<?php
/**
 * includes/form.php
 * Shared form used by create.php and edit.php.
 * Expects: $v (values), $errors, $types (vehicle types), $submitLabel
 */
function field_class(array $errors, string $name): string
{
    return 'form-control' . (isset($errors[$name]) ? ' is-invalid' : '');
}
function field_error(array $errors, string $name): void
{
    if (isset($errors[$name])) {
        echo '<div class="invalid-feedback">' . e($errors[$name]) . '</div>';
    }
}
?>
<form method="post" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <h2 class="h6 form-section">Vehicle details</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="plate_number" class="form-label">Plate number</label>
            <input type="text" class="<?= field_class($errors, 'plate_number') ?> text-uppercase" id="plate_number"
                   name="plate_number" maxlength="15" placeholder="ABC 1234" value="<?= e($v['plate_number']) ?>" required>
            <?php field_error($errors, 'plate_number'); ?>
        </div>
        <div class="col-md-4">
            <label for="type_id" class="form-label">Vehicle type</label>
            <select class="form-select <?= isset($errors['type_id']) ? 'is-invalid' : '' ?>" id="type_id" name="type_id" required>
                <option value="">Choose a type</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= (int)$t['type_id'] ?>" <?= (string)$v['type_id'] === (string)$t['type_id'] ? 'selected' : '' ?>>
                        <?= e($t['type_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php field_error($errors, 'type_id'); ?>
        </div>
        <div class="col-md-4">
            <label for="color" class="form-label">Color</label>
            <input type="text" class="<?= field_class($errors, 'color') ?>" id="color" name="color" maxlength="30"
                   value="<?= e($v['color']) ?>" required>
            <?php field_error($errors, 'color'); ?>
        </div>
        <div class="col-md-4">
            <label for="brand" class="form-label">Brand</label>
            <input type="text" class="<?= field_class($errors, 'brand') ?>" id="brand" name="brand" maxlength="50"
                   placeholder="Toyota" value="<?= e($v['brand']) ?>" required>
            <?php field_error($errors, 'brand'); ?>
        </div>
        <div class="col-md-4">
            <label for="model" class="form-label">Model</label>
            <input type="text" class="<?= field_class($errors, 'model') ?>" id="model" name="model" maxlength="50"
                   placeholder="Vios" value="<?= e($v['model']) ?>" required>
            <?php field_error($errors, 'model'); ?>
        </div>
        <div class="col-md-4">
            <label for="year_model" class="form-label">Year model</label>
            <input type="number" class="<?= field_class($errors, 'year_model') ?>" id="year_model" name="year_model"
                   min="1950" max="<?= (int)date('Y') + 1 ?>" value="<?= e($v['year_model']) ?>" required>
            <?php field_error($errors, 'year_model'); ?>
        </div>
    </div>

    <h2 class="h6 form-section">Owner and registration</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="owner_name" class="form-label">Owner's full name</label>
            <input type="text" class="<?= field_class($errors, 'owner_name') ?>" id="owner_name" name="owner_name"
                   maxlength="100" value="<?= e($v['owner_name']) ?>" required>
            <?php field_error($errors, 'owner_name'); ?>
        </div>
        <div class="col-md-6">
            <label for="contact_number" class="form-label">Contact number</label>
            <input type="tel" class="<?= field_class($errors, 'contact_number') ?>" id="contact_number" name="contact_number"
                   maxlength="20" placeholder="09171234567" value="<?= e($v['contact_number']) ?>" required>
            <?php field_error($errors, 'contact_number'); ?>
        </div>
        <div class="col-md-6">
            <label for="registration_expiry" class="form-label">Registration expiry</label>
            <input type="date" class="<?= field_class($errors, 'registration_expiry') ?>" id="registration_expiry"
                   name="registration_expiry" value="<?= e($v['registration_expiry']) ?>" required>
            <?php field_error($errors, 'registration_expiry'); ?>
        </div>
        <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                <?php foreach (STATUS_OPTIONS as $s): ?>
                    <option value="<?= e($s) ?>" <?= $v['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
            <?php field_error($errors, 'status'); ?>
        </div>
        <div class="col-12">
            <label for="remarks" class="form-label">Remarks <span class="text-body-secondary">(optional)</span></label>
            <textarea class="<?= field_class($errors, 'remarks') ?>" id="remarks" name="remarks" rows="3"
                      maxlength="1000"><?= e($v['remarks']) ?></textarea>
            <?php field_error($errors, 'remarks'); ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-brand"><i class="bi bi-check-lg me-1"></i><?= e($submitLabel) ?></button>
        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
