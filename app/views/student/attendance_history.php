<div class="page-header">
    <h1><i class="fas fa-history"></i> Lịch sử điểm danh</h1>
    <p class="page-description">Xem toàn bộ lịch sử điểm danh của bạn</p>
</div>

<?php if (empty($records)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Chưa có dữ liệu</h3>
        <p>Chưa có dữ liệu điểm danh nào</p>
    </div>
<?php else: ?>
    <div class="attendance-stats">
        <div class="stat-small">
            <div class="stat-value"><?= count($records) ?></div>
            <div class="stat-label">Tổng buổi học</div>
        </div>
        <div class="stat-small">
            <div class="stat-value" style="color: #10b981;">
                <?= count(array_filter($records, fn($r) => $r['status'] === 'present')) ?>
            </div>
            <div class="stat-label">Có mặt</div>
        </div>
        <div class="stat-small">
            <div class="stat-value" style="color: #ef4444;">
                <?= count(array_filter($records, fn($r) => $r['status'] === 'absent')) ?>
            </div>
            <div class="stat-label">Vắng</div>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Thời gian</th>
                    <th>Môn học</th>
                    <th>Học kỳ</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><strong><?= date('d/m/Y', strtotime($r['session_date'])) ?></strong></td>
                    <td><?= htmlspecialchars($r['start_time']) ?> - <?= htmlspecialchars($r['end_time']) ?></td>
                    <td><?= htmlspecialchars($r['subject_code']) ?> - <?= htmlspecialchars($r['subject_name']) ?></td>
                    <td><?= htmlspecialchars($r['semester_name']) ?></td>
                    <td>
                        <?php if ($r['is_makeup']): ?>
                            <span class="badge badge-warning"><i class="fas fa-sync"></i> Buổi bù</span>
                        <?php else: ?>
                            <span class="badge badge-muted"><i class="fas fa-calendar"></i> Thường</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['status'] === 'present'): ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Có mặt</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Vắng</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
