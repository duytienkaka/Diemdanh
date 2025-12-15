<div class="list-header">
    <div>
        <h2><i class="fas fa-user-graduate"></i> Quản lý sinh viên</h2>
        <p class="list-description">Quản lý thông tin và trạng thái sinh viên</p>
    </div>
    <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm sinh viên mới
    </a>
</div>

    <?php if (empty($students)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Chưa có sinh viên</h3>
        <p>Hãy thêm sinh viên đầu tiên để bắt đầu</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Giới tính</th>
                    <th>Điện thoại</th>
                    <th>Tài khoản</th>
                    <th>Role</th>
                    <th>Trạng thái</th>
                    <th style="width: 160px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $st): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($st['student_code'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($st['full_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($st['gender'] ?? '')?></td>
                        <td><?= htmlspecialchars($st['phone'] ?? '') ?></td>
                        <td><code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($st['username'] ?? '') ?></code></td>
                        <td>
                            <?php if ($st['is_monitor']): ?>
                                <span class="badge badge-warning"><i class="fas fa-star"></i> Lớp trưởng</span>
                            <?php else: ?>
                                <span class="badge badge-muted">Sinh viên</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($st['is_active']): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Đang học</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><i class="fas fa-lock"></i> Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons" style="display:flex; gap:6px;">
                                <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=edit&id=<?= $st['id'] ?>" class="btn btn-sm btn-edit" title="Sửa"><i class="fas fa-edit"></i></a>
                                <?php if ($st['is_active']): ?>
                                    <?php if (!$st['is_monitor']): ?>
                                        <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=setMonitor&id=<?= $st['id'] ?>" class="btn btn-sm btn-star" onclick="return confirm('Đặt sinh viên này làm lớp trưởng?');" title="Đặt làm lớp trưởng"><i class="fas fa-star"></i></a>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=delete&id=<?= $st['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Khóa (ngừng active) sinh viên này?');" title="Khóa"><i class="fas fa-lock"></i></a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=setActive&id=<?= $st['id'] ?>&act=1" class="btn btn-sm btn-success" onclick="return confirm('Mở khóa (active) lại sinh viên này?');" title="Mở khóa"><i class="fas fa-unlock"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>