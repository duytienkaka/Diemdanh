<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Thống kê vắng học</h1>
    <p class="page-description">Tóm tắt số buổi vắng của mỗi sinh viên</p>
</div>

<div style="margin-bottom: 16px;">
    <form method="get" style="display:flex; gap:8px; align-items:center;">
        <input type="hidden" name="controller" value="admin_report">
        <input type="hidden" name="action" value="index">
        <label style="font-size:13px;">Từ:</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>">
        <label style="font-size:13px;">Đến:</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>">
        <div class="filter-actions">
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Lọc</button>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>index.php?controller=admin_report&action=index"><i class="fas fa-eraser"></i> Bỏ lọc</a>
        </div>
    </form>
</div>

<?php
    $today = date('Y-m-d');
    if (($start_date ?? '') === $today && ($end_date ?? '') === $today): ?>
    <div style="margin-bottom:12px; color:#374151; font-size:13px;">
        Hiện: <strong>Hôm nay</strong>
    </div>
<?php elseif (!empty($start_date) || !empty($end_date)): ?>
    <div style="margin-bottom:12px; color:#374151; font-size:13px;">
        Lọc: <?php if (!empty($start_date)): ?>Từ <strong><?= htmlspecialchars($start_date ?? '') ?></strong><?php endif; ?> <?php if (!empty($end_date)): ?>Đến <strong><?= htmlspecialchars($end_date ?? '') ?></strong><?php endif; ?>
    </div>
<?php endif; ?>

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
        <div class="overview-item students">
            <div class="overview-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="overview-label">Tổng sinh viên</div>
            <div class="overview-value"><?= count($summary) ?></div>
            <div class="overview-meta">Số sinh viên đang theo dõi</div>
        </div>

        <div class="overview-item absences">
            <div class="overview-icon"><i class="fas fa-calendar-times"></i></div>
            <div class="overview-label">Tổng lần vắng</div>
            <div class="overview-value"><?= array_sum(array_column($summary, 'total_absent')) ?></div>
            <div class="overview-meta">Tổng số lượt vắng đã ghi</div>
        </div>

        <div class="overview-item max-absent">
            <div class="overview-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="overview-label">Vắng nhiều nhất</div>
            <div class="overview-value"><?= max(array_column($summary, 'total_absent')) ?></div>
            <div class="overview-meta">Số lần vắng cao nhất</div>
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
                    <td><strong><?= htmlspecialchars($row['student_code'] ?? '') ?></strong></td>
                    <td><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
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
                                <?php
                                $q = 'id=' . intval($row['student_id']);
                                if (!empty($start_date)) $q .= '&start_date=' . urlencode($start_date);
                                if (!empty($end_date)) $q .= '&end_date=' . urlencode($end_date);
                                ?>
                                <a href="<?= BASE_URL ?>index.php?controller=admin_report&action=studentDetail&<?= $q ?>"
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
