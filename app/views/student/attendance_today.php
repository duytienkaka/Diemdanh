<div class="page-header">
    <h1><i class="fas fa-clipboard-check"></i> Điểm danh hôm nay</h1>
    <p class="page-description">Kiểm tra trạng thái điểm danh của bạn</p>
</div>

<?php if (!$record): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3>Không có buổi học hôm nay</h3>
        <p>Hôm nay bạn không có buổi học nào, hoặc lớp trưởng chưa điểm danh.</p>
    </div>
<?php else: ?>
    <div class="attendance-card">
        <div class="attendance-header">
            <div class="attendance-title">
                <h2><?= htmlspecialchars($record['subject_code']) ?> - <?= htmlspecialchars($record['subject_name']) ?></h2>
                <p class="attendance-date" style="font-size: 14px; color: #6b7280; margin-top: 4px;">
                    <i class="fas fa-calendar"></i>
                    <?= date('d/m/Y', strtotime($record['session_date'])) ?>
                    <span style="margin-left: 16px;"><i class="fas fa-clock"></i>
                    <?= htmlspecialchars($record['start_time']) ?> - <?= htmlspecialchars($record['end_time']) ?></span>
                </p>
            </div>
            <div class="attendance-status">
                <?php if ($record['status'] === 'present'): ?>
                    <span class="badge badge-success" style="font-size: 14px; padding: 10px 14px;">
                        <i class="fas fa-check-circle"></i> Có mặt
                    </span>
                <?php else: ?>
                    <span class="badge badge-danger" style="font-size: 14px; padding: 10px 14px;">
                        <i class="fas fa-times-circle"></i> Vắng
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="attendance-info">
            <div class="info-row">
                <div class="info-col">
                    <div class="info-label"><i class="fas fa-layer-group"></i> Học kỳ</div>
                    <div class="info-value"><?= htmlspecialchars($record['semester_name']) ?></div>
                </div>
                <div class="info-col">
                    <div class="info-label"><i class="fas fa-tag"></i> Trạng thái buổi học</div>
                    <div class="info-value">
                        <?php if ($record['session_status'] === 'ended'): ?>
                            <span class="badge badge-success">Đã kết thúc</span>
                        <?php elseif ($record['session_status'] === 'ongoing'): ?>
                            <span class="badge badge-info">Đang diễn ra</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Chưa bắt đầu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
