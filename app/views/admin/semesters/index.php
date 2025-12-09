<div class="list-header">
    <div>
        <h2><i class="fas fa-calendar-alt"></i> Quản lý học kỳ</h2>
        <p class="list-description">Tạo và quản lý các học kỳ trong năm học</p>
    </div>
    <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm học kỳ
    </a>
</div>

<?php if (empty($semesters)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3>Chưa có học kỳ</h3>
        <p>Hãy tạo học kỳ đầu tiên để bắt đầu</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tên học kỳ</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th style="width: 120px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($semesters as $sem): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($sem['name']) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($sem['start_date'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($sem['end_date'])) ?></td>
                        <td>
                            <?php if ($sem['is_active']): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Đang dùng</span>
                            <?php else: ?>
                                <span class="badge badge-muted"><i class="fas fa-eye-slash"></i> Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons" style="display:flex; gap:6px;">
                                <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=edit&id=<?= $sem['id'] ?>" class="btn btn-sm btn-edit" title="Sửa"><i class="fas fa-edit"></i></a>
                                <?php if ($sem['is_active']): ?>
                                    <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=setActive&id=<?= $sem['id'] ?>&act=0" class="btn btn-sm btn-danger" onclick="return confirm('Ẩn học kỳ này?');" title="Ẩn"><i class="fas fa-eye-slash"></i></a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=setActive&id=<?= $sem['id'] ?>&act=1" class="btn btn-sm btn-success" onclick="return confirm('Kích hoạt học kỳ này?');" title="Kích hoạt"><i class="fas fa-check-circle"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
