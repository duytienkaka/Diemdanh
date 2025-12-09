<div class="page-header">
    <h1><i class="fas fa-file-chart-line"></i> Chi tiết vắng học</h1>
    <p class="page-description">
        Sinh viên: <strong><?= htmlspecialchars($student['student_code']) ?> - <?= htmlspecialchars($student['full_name']) ?></strong>
    </p>
</div>

<?php if (empty($records)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3>Không có buổi vắng</h3>
        <p>Sinh viên này chưa vắng buổi nào</p>
    </div>
<?php else: ?>
    <div class="absence-stats">
        <div class="stat-box">
            <div class="stat-number"><?= count($records) ?></div>
            <div class="stat-title">Tổng buổi vắng</div>
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
                            <span style="background: #fef3c7; color: #b45309; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                <i class="fas fa-sync"></i> Buổi bù
                            </span>
                        <?php else: ?>
                            <span style="background: #e5e7eb; color: #6b7280; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                <i class="fas fa-calendar"></i> Thường
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
