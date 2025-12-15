<div class="page-header">
    <h1><i class="fas fa-list-check"></i> Chi tiết điểm danh buổi học</h1>
</div>

<?php
// Tính trạng thái thực tế của buổi học
$now   = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$today = $now->format('Y-m-d');
$time  = $now->format('H:i:s');

$effectiveStatus = $session['status'];

if ($effectiveStatus !== 'ended') {
    if (
        $session['session_date'] < $today ||
        ($session['session_date'] === $today && $session['end_time'] <= $time)
    ) {
        $effectiveStatus = 'ended';
    } elseif (
        $session['session_date'] === $today &&
        $session['start_time']   <= $time &&
        $session['end_time']     >  $time
    ) {
        $effectiveStatus = 'ongoing';
    } else {
        $effectiveStatus = 'scheduled';
    }
}
?>

<div class="session-info-card">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label"><i class="fas fa-book"></i> Môn</div>
            <div class="info-value"><?= htmlspecialchars($session['subject_code'] ?? '') ?> - <?= htmlspecialchars($session['subject_name'] ?? '') ?></div>
            <div class="info-sub"><?= htmlspecialchars($session['semester_name'] ?? '') ?></div>
        </div>

        <div class="info-item">
            <div class="info-label"><i class="fas fa-calendar"></i> Ngày</div>
            <div class="info-value"><?= date('d/m/Y', strtotime($session['session_date'])) ?></div>
            <div class="info-sub"><?= htmlspecialchars($session['start_time'] ?? '') ?> - <?= htmlspecialchars($session['end_time'] ?? '') ?></div>
        </div>

        <div class="info-item">
            <div class="info-label"><i class="fas fa-tag"></i> Loại</div>
            <div class="info-value">
                <?php if ($session['is_makeup']): ?>
                    <span class="badge badge-warning">Buổi bù</span>
                <?php else: ?>
                    <span class="badge badge-muted">Buổi thường</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="info-item">
            <div class="info-label"><i class="fas fa-info-circle"></i> Trạng thái</div>
            <div class="info-value">
                <?php if ($effectiveStatus === 'ended'): ?>
                    <span class="badge badge-success">Đã kết thúc</span>
                <?php elseif ($effectiveStatus === 'ongoing'): ?>
                    <span class="badge badge-warning"><i class="fas fa-play"></i> Đang diễn ra</span>
                <?php else: ?>
                    <span class="badge badge-warning"><i class="fas fa-calendar"></i> Sắp tới</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (empty($attendanceList)): ?>
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <h3>Chưa có dữ liệu điểm danh</h3>
        <p>Buổi học này chưa được điểm danh</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ tên</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceList as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['student_code'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
                        <td>
                            <?php
                            $st = $row['status'] ?? 'present';
                            if ($st === 'present') {
                                echo '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Có mặt</span>';
                            } elseif ($st === 'late') {
                                echo '<span class="badge badge-warning"><i class="fas fa-clock"></i> Đi muộn</span>';
                            } elseif ($st === 'truant') {
                                echo '<span class="badge badge-danger"><i class="fas fa-user-slash"></i> Trốn tiết</span>';
                            } else {
                                echo '<span class="badge badge-muted"><i class="fas fa-times-circle"></i> Vắng</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div style="margin-top: 24px;">
    <a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=history" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>