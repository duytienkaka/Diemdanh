<div class="page-header">
    <h1><i class="fas fa-history"></i> Lịch sử điểm danh</h1>
    <p class="page-description">Xem các buổi học đã được điểm danh</p>
</div>

<div class="form-card">
    <form method="get" action="" style="display: flex; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="controller" value="monitor_attendance">
        <input type="hidden" name="action" value="history">

        <div class="form-group" style="flex: 1; margin-bottom: 0;">
            <label>Lọc theo ngày:</label>
            <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>" style="margin-bottom: 0;">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
            <i class="fas fa-filter"></i> Lọc
        </button>
    </form>
</div>
<?php
    $now   = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $today = $now->format('Y-m-d');
    $time  = $now->format('H:i:s');
?>
<?php if (empty($sessions)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Không có buổi học nào</h3>
        <p>Chưa có buổi học nào được điểm danh</p>
    </div>
<?php else: ?>
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
                    <th style="width: 100px;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sessions as $s): ?>
            <?php
                // Tính trạng thái thực tế cho từng buổi
                $effectiveStatus = $s['status'];

                if ($effectiveStatus !== 'ended') {
                    if (
                        $s['session_date'] < $today ||
                        ($s['session_date'] === $today && $s['end_time'] <= $time)
                    ) {
                        $effectiveStatus = 'ended';
                    } elseif (
                        $s['session_date'] === $today &&
                        $s['start_time']   <= $time &&
                        $s['end_time']     >  $time
                    ) {
                        $effectiveStatus = 'ongoing';
                    } else {
                        $effectiveStatus = 'scheduled';
                    }
                }
            ?>
            <tr>
                <td><strong><?= date('d/m/Y', strtotime($s['session_date'])) ?></strong></td>
                <td><?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></td>
                <td><?= htmlspecialchars($s['subject_code']) ?> - <?= htmlspecialchars($s['subject_name']) ?></td>
                <td><?= htmlspecialchars($s['semester_name']) ?></td>
                <td>
                    <?php if ($s['is_makeup']): ?>
                        <span class="badge badge-warning"><i class="fas fa-sync"></i> Buổi bù</span>
                    <?php else: ?>
                        <span class="badge badge-muted"><i class="fas fa-calendar"></i> Thường</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($effectiveStatus === 'ended'): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Đã kết thúc</span>
                    <?php elseif ($effectiveStatus === 'ongoing'): ?>
                        <span class="badge badge-warning"><i class="fas fa-play"></i> Đang diễn ra</span>
                    <?php else: ?>
                        <span class="badge badge-warning"><i class="fas fa-calendar"></i> Sắp tới</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=viewSession&id=<?= $s['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

            </tbody>
        </table>
    </div>
<?php endif; ?>
