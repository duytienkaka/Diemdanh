<div class="page-header">
    <h1><i class="fas fa-calendar-days"></i> Lịch học của bạn</h1>
    <p class="page-description">
        Từ <strong><?= date('d/m/Y', strtotime($startDate)) ?></strong>
        đến <strong><?= date('d/m/Y', strtotime($endDate)) ?></strong>
    </p>
</div>

<?php if (empty($sessions)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Không có buổi học</h3>
        <p>Trong khoảng thời gian này không có buổi học nào</p>
    </div>
<?php else: ?>
    <div class="schedule-grid">
        <?php foreach ($sessions as $s): ?>
            <div class="schedule-card">
                <div class="schedule-date">
                    <div class="date-day"><?= date('d', strtotime($s['session_date'])) ?></div>
                    <div class="date-month"><?= date('M', strtotime($s['session_date'])) ?></div>
                </div>
                <div class="schedule-content">
                    <h3><?= htmlspecialchars($s['subject_code']) ?></h3>
                    <p class="schedule-title"><?= htmlspecialchars($s['subject_name']) ?></p>
                    <div class="schedule-details">
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-door-open"></i>
                            <?= htmlspecialchars($s['room'] ?? 'Không xác định') ?>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-layer-group"></i>
                            <?= htmlspecialchars($s['semester_name']) ?>
                        </div>
                    </div>
                    <div class="schedule-tags">
                        <?php if ($s['is_makeup']): ?>
                            <span class="tag tag-warning">
                                <i class="fas fa-sync"></i> Buổi bù
                            </span>
                        <?php endif; ?>
                        <span class="tag tag-status">
                            <?php if ($s['status'] === 'ended'): ?>
                                <i class="fas fa-check"></i> Đã kết thúc
                            <?php elseif ($s['status'] === 'ongoing'): ?>
                                <i class="fas fa-play-circle"></i> Đang diễn ra
                            <?php else: ?>
                                <i class="fas fa-calendar"></i> Sắp tới
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="table-responsive" style="margin-top: 24px;">
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Thời gian</th>
                    <th>Môn học</th>
                    <th>Học kỳ</th>
                    <th>Phòng</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><strong><?= date('d/m/Y', strtotime($s['session_date'])) ?></strong></td>
                    <td><?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></td>
                    <td><?= htmlspecialchars($s['subject_code']) ?> - <?= htmlspecialchars($s['subject_name']) ?></td>
                    <td><?= htmlspecialchars($s['semester_name']) ?></td>
                    <td><?= htmlspecialchars($s['room'] ?? '-') ?></td>
                    <td>
                        <?php if ($s['is_makeup']): ?>
                            <span class="badge badge-warning"><i class="fas fa-sync"></i> Buổi bù</span>
                        <?php else: ?>
                            <span class="badge badge-muted"><i class="fas fa-calendar"></i> Thường</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($s['status'] === 'ended'): ?>
                            <span class="badge badge-muted"><i class="fas fa-check"></i> Đã kết thúc</span>
                        <?php elseif ($s['status'] === 'ongoing'): ?>
                            <span class="badge badge-info"><i class="fas fa-play-circle"></i> Đang diễn ra</span>
                        <?php else: ?>
                            <span class="badge badge-warning"><i class="fas fa-calendar"></i> Sắp tới</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
