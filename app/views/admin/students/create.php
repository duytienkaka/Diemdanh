<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Thêm sinh viên mới</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <div><i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong></div>
        <?php foreach ($errors as $e): ?>
            <p style="margin: 4px 0 0 24px;">• <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="" class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label>Username (đăng nhập)</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>MSSV</label>
            <input type="text" name="student_code" required>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="full_name" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Giới tính</label>
            <select name="gender">
                <option value="">-- Chọn --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>

        <div class="form-group">
            <label>Điện thoại</label>
            <input type="text" name="phone">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_monitor">
                <span>Đặt làm lớp trưởng</span>
            </label>
        </div>
    </div>

    <div class="form-actions" style="display: flex; gap: 8px; margin-top: 24px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
        <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=index" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</form>
