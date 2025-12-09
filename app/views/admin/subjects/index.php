<div class="page-header">
    <h1><i class="fas fa-book-open"></i> Quản lý môn học</h1>
    <div class="page-actions">
        <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=create" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm môn học</a>
    </div>
</div>

<?php if (empty($subjects)): ?>
    <div class="empty-state">
        <p>Chưa có môn học nào.</p>
        <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=create" class="btn btn-primary">Thêm môn học</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã môn</th>
                    <th>Tên môn</th>
                    <th>Học kỳ</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['code']) ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['semester_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($s['description']) ?></td>
                        <td>
                            <?php if ($s['is_active']): ?>
                                <span class="badge badge-success">Đang dùng</span>
                            <?php else: ?>
                                <span class="badge badge-muted">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-edit">Sửa</a>
                            <?php if ($s['is_active']): ?>
                                <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=setActive&id=<?= $s['id'] ?>&act=0" onclick="return confirm('Ẩn môn học này?');" class="btn btn-sm btn-danger">Ẩn</a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=setActive&id=<?= $s['id'] ?>&act=1" class="btn btn-sm">Kích hoạt</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
