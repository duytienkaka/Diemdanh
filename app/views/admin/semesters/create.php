<div class="page-header">
    <h1><i class="fas fa-calendar-plus"></i> Thêm học kỳ</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message ?? '') ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <div><i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong></div>
        <?php foreach ($errors as $e): ?><p style="margin: 4px 0 0 24px;">• <?= htmlspecialchars($e ?? '') ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="" class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label>Tên học kỳ</label>
            <input type="text" name="name" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Ngày bắt đầu</label>
            <input type="date" name="start_date" required>
        </div>

        <div class="form-group">
            <label>Ngày kết thúc</label>
            <input type="date" name="end_date" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" checked>
                <span>Đang sử dụng</span>
            </label>
        </div>
    </div>

    <div class="form-actions" style="display: flex; gap: 8px; margin-top: 24px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
        <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=index" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</form>
