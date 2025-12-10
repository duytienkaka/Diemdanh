<div class="page-header">
    <h1><i class="fas fa-calendar-check"></i> Điểm danh buổi học hôm nay</h1>
    <p class="page-description">Ghi nhận các sinh viên có mặt/vắng mặt</p>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" style="margin-bottom: 24px;">
        <div style="display: flex; gap: 8px; align-items: flex-start;">
            <i class="fas fa-exclamation-circle" style="flex-shrink: 0; margin-top: 2px;"></i>
            <div>
                <?php foreach ($errors as $e): ?>
                    <p style="margin: 4px 0;"><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($message)): ?>
    <div class="alert alert-success" style="margin-bottom: 24px;">
        <div style="display: flex; gap: 8px; align-items: flex-start;">
            <i class="fas fa-check-circle" style="flex-shrink: 0; margin-top: 2px;"></i>
            <div>
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($sessions)): ?>
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3>Hôm nay không có buổi học</h3>
        <p>Vui lòng quay lại vào những ngày có buổi học</p>
    </div>
<?php else: ?>

    <div class="card" style="margin-bottom: 24px;">
        <div style="padding: 16px;">
            <form method="get" action="" style="display: flex; gap: 12px; align-items: flex-end;">
                <input type="hidden" name="controller" value="monitor_attendance">
                <input type="hidden" name="action" value="today">

                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label><i class="fas fa-calendar-check"></i> Chọn buổi học hôm nay:</label>
                    <select name="session_id" onchange="this.form.submit()" style="margin-bottom: 0;">
                        <option value="">-- Chọn buổi học --</option>
                        <?php foreach ($sessions as $ses): ?>
                            <option value="<?= $ses['id'] ?>"
                                <?= ($selectedSession && $selectedSession['id'] == $ses['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ses['start_time']) ?> - <?= htmlspecialchars($ses['end_time']) ?>
                                | <?= htmlspecialchars($ses['subject_code']) ?> - <?= htmlspecialchars($ses['subject_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectedSession): ?>
        <?php
            // Tính trạng thái thực tế của buổi đang chọn
            $now   = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
            $today = $now->format('Y-m-d');
            $time  = $now->format('H:i:s');

            $start = $selectedSession['start_time'];
            $end   = $selectedSession['end_time'];

            // Nếu end_time <= start_time (ví dụ nhập 00:00) thì coi như 23:59:59 để tránh bị "đã kết thúc" sớm
            if ($end <= $start) {
                $end = '23:59:59';
            }

            $effectiveStatus = $selectedSession['status'];

            if ($selectedSession['session_date'] < $today) {
                $effectiveStatus = 'ended';
            } elseif ($selectedSession['session_date'] > $today) {
                $effectiveStatus = 'scheduled';
            } else {
                // Hôm nay
                if ($time < $start) {
                    $effectiveStatus = 'scheduled';   // Chưa đến giờ
                } elseif ($time >= $start && $time < $end) {
                    $effectiveStatus = 'ongoing';    // Đang diễn ra
                } else {
                    $effectiveStatus = 'ended';      // Đã qua giờ
                }
            }
        ?>
        <?php
        if ($selectedSession['session_date'] === $today && $effectiveStatus === 'scheduled') {
            $tz      = new DateTimeZone('Asia/Ho_Chi_Minh');
            $startDt = new DateTime($selectedSession['session_date'] . ' ' . $start, $tz);
            $nowDt   = new DateTime('now', $tz);

            $diffSeconds = $startDt->getTimestamp() - $nowDt->getTimestamp();

            // Chỉ set timeout nếu còn > 0 và trong vòng 4 tiếng
            if ($diffSeconds > 0 && $diffSeconds <= 4 * 3600) {
                echo '<script>
                    setTimeout(function () {
                        location.reload();
                    }, ' . ($diffSeconds * 1000) . ');
                </script>';
            }
        }
        ?>

        <div class="card" style="margin-bottom: 24px;">
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-book"></i> Môn học
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                            <?= htmlspecialchars($selectedSession['subject_code']) ?> - <?= htmlspecialchars($selectedSession['subject_name']) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-calendar"></i> Ngày
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                            <?= date('d/m/Y', strtotime($selectedSession['session_date'])) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-clock"></i> Giờ
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                            <?= htmlspecialchars($selectedSession['start_time']) ?> - <?= htmlspecialchars($selectedSession['end_time']) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-layer-group"></i> Học kỳ
                        </div>
                        <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                            <?= htmlspecialchars($selectedSession['semester_name']) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-tag"></i> Trạng thái
                        </div>
                        <div>
                            <?php if ($effectiveStatus === 'ended'): ?>
                                <span class="badge badge-danger"><i class="fas fa-check"></i> Đã kết thúc</span>
                            <?php elseif ($effectiveStatus === 'ongoing'): ?>
                                <span class="badge badge-success"><i class="fas fa-play"></i> Đang diễn ra</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Chưa bắt đầu</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-check-double"></i> Điểm danh
                        </div>
                        <div>
                            <?php if ($selectedSession['is_attendance_done']): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Hoàn thành</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-circle"></i> Chưa hoàn</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="">
            <input type="hidden" name="session_id" value="<?= $selectedSession['id'] ?>">

            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 80px;">MSSV</th>
                                <th>Họ tên</th>
                                <th style="width: 100px; text-align: center;">Vắng</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($attendanceList)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 32px;">
                                    <i class="fas fa-inbox" style="font-size: 24px; color: #d1d5db; margin-bottom: 8px; display: block;"></i>
                                    <p style="color: #6b7280;">Chưa có sinh viên nào.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendanceList as $row): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['student_code']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td style="text-align: center;">
                                        <input type="checkbox"
                                               name="absent_ids[]"
                                               value="<?= $row['student_id'] ?>"
                                               <?= $row['status'] === 'absent' ? 'checked' : '' ?>
                                               style="width: 18px; height: 18px; cursor: pointer;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <?php if ($effectiveStatus === 'ended'): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; flex: 1; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-lock"></i>
                        <span>Buổi học đã kết thúc, không thể chỉnh sửa.</span>
                    </div>
                <?php elseif ($effectiveStatus === 'scheduled'): ?>
                    <div style="background: #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; flex: 1; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-clock"></i>
                        <span>Chưa đến giờ học, không thể điểm danh.</span>
                    </div>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Lưu điểm danh
                    </button>
                    <a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=today" class="btn btn-muted">
                        <i class="fas fa-redo"></i> Tải lại
                    </a>
                <?php endif; ?>
            </div>
        </form>

    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Chọn một buổi học</h3>
            <p>Vui lòng chọn buổi học từ danh sách trên để bắt đầu điểm danh</p>
        </div>
    <?php endif; ?>

<?php endif; ?>
