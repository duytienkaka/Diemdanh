<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Thống kê vắng học</h1>
    <p class="page-description">Tóm tắt số buổi vắng của mỗi sinh viên</p>
</div>

<?php if (empty($summary)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Không có dữ liệu</h3>
        <p>Chưa có dữ liệu vắng học</p>
    </div>
<?php else: ?>
    <div class="stats-overview">
        <div class="overview-item">
            <div class="overview-label">Tổng sinh viên</div>
            <div class="overview-value"><?= count($summary) ?></div>
        </div>
        <div class="overview-item">
            <div class="overview-label">Tổng lần vắng</div>
            <div class="overview-value"><?= array_sum(array_column($summary, 'total_absent')) ?></div>
        </div>
        <div class="overview-item">
            <div class="overview-label">Vắng nhiều nhất</div>
            <div class="overview-value"><?= max(array_column($summary, 'total_absent')) ?></div>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th style="width: 120px; text-align: center;">Tổng vắng</th>
                    <th style="width: 100px; text-align: center;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($summary as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['student_code']) ?></strong></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td style="text-align: center;">
                        <?php 
                            $absent = $row['total_absent'];
                            if ($absent > 3): ?>
                                <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> <?= $absent ?></span>
                            <?php elseif ($absent > 0): ?>
                                <span class="badge badge-warning"><i class="fas fa-info-circle"></i> <?= $absent ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> 0</span>
                            <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <a href="<?= BASE_URL ?>index.php?controller=admin_report&action=studentDetail&id=<?= $row['student_id'] ?>"
                           class="button" style="padding: 4px 8px; font-size: 12px;">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
