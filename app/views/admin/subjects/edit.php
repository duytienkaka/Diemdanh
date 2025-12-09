<div class="page-header">
    <h1><i class="fas fa-book-edit"></i> Sửa môn học</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <div><i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong></div>
        <?php foreach ($errors as $e): ?><p style="margin: 4px 0 0 24px;">• <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="" class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label>Mã môn</label>
            <input type="text" name="code" value="<?= htmlspecialchars($subject['code']) ?>" required>
        </div>

        <div class="form-group">
            <label>Tên môn</label>
            <input type="text" name="name" value="<?= htmlspecialchars($subject['name']) ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label>Học kỳ</label>
            <select name="semester_id" required>
                <option value="">-- Chọn học kỳ --</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>"
                        <?= (int)$subject['semester_id'] === (int)$sem['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sem['name']) ?>
                        (<?= htmlspecialchars($sem['start_date']) ?> - <?= htmlspecialchars($sem['end_date']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label>Mô tả</label>
            <textarea name="description" rows="4"><?= htmlspecialchars($subject['description']) ?></textarea>
        </div>
    </div>

    <div class="form-actions" style="display: flex; gap: 8px; margin-top: 24px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
        <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=index" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</form>
