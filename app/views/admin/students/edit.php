<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Sửa thông tin sinh viên</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message ?? '') ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <div><i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong></div>
        <?php foreach ($errors as $e): ?>
            <p style="margin: 4px 0 0 24px;">• <?= htmlspecialchars($e ?? '') ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-card">
    <div style="padding: 0 0 16px 0; border-bottom: 1px solid #e5e7eb; margin-bottom: 16px;">
        <p><strong>Username:</strong> <code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($student['username'] ?? '') ?></code></p>
    </div>

    <form method="post" action="">
        <div class="form-row">
            <div class="form-group">
                <label>MSSV</label>
                <input type="text" name="student_code" value="<?= htmlspecialchars($student['student_code'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Giới tính</label>
                <select name="gender">
                    <option value="">-- Chọn --</option>
                    <option value="male"   <?= $student['gender'] === 'male' ? 'selected' : '' ?>>Nam</option>
                    <option value="female" <?= $student['gender'] === 'female' ? 'selected' : '' ?>>Nữ</option>
                    <option value="other"  <?= $student['gender'] === 'other' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <div class="form-group">
                <label>Điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions" style="display: flex; gap: 8px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
            <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=index" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </div>
    </form>
</div>
