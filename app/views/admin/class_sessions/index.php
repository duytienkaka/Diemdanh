<div class="page-header">
    <h1><i class="fas fa-list"></i> Danh sách buổi học</h1>
    <div class="page-actions">
        <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createSchedule" class="btn btn-primary"><i class="fas fa-plus"></i> Tạo thời khóa biểu</a>
        <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createMakeup" class="btn btn-muted"><i class="fas fa-sync-alt"></i> Tạo buổi bù</a>
    </div>
</div>

<div class="form-card">
    <form method="get" action="">
        <input type="hidden" name="controller" value="admin_class_session">
        <input type="hidden" name="action" value="index">

        <div class="form-row">
            <div class="form-group">
                <label>Học kỳ</label>
                <select name="semester_id">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['id'] ?>" <?= ($semesterId == $sem['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Môn học</label>
                <select name="subject_id">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>" <?= ($subjectId == $sub['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sub['code']) ?> - <?= htmlspecialchars($sub['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Ngày học</label>
                <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=index" class="btn btn-muted">Xoá lọc</a>
            </div>
        </div>
    </form>
</div>

<?php if (empty($sessions)): ?>
    <div class="empty-state">
        <p>Không có buổi học nào phù hợp.</p>
        <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createSchedule" class="btn btn-primary">Tạo thời khóa biểu</a>
    </div>
<?php else: ?>

    <?php
    // Lấy thời gian hiện tại (múi giờ VN – giả sử bạn đã set ở index.php,
    // nếu chưa thì dòng này vẫn OK vì tự dùng timezone mặc định)
    $now   = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
    $today = $now->format('Y-m-d');
    $time  = $now->format('H:i:s');
    ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Học kỳ</th>
                    <th>Môn</th>
                    <th>Ngày</th>
                    <th>Giờ</th>
                    <th>Phòng</th>
                    <th>Loại</th>
                    <th>Trạng thái</th>
                    <th>Điểm danh</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $s): ?>
                        <?php
                            // Tính trạng thái thực tế dựa trên ngày/giờ hiện tại
                            $effectiveStatus = $s['status'];

                            if ($s['session_date'] < $today) {
                                // Ngày học đã qua
                                $effectiveStatus = 'ended';
                            } elseif ($s['session_date'] > $today) {
                                // Ngày học trong tương lai
                                $effectiveStatus = 'scheduled';
                            } else {
                                // Hôm nay
                                if ($time < $s['start_time']) {
                                    // Chưa tới giờ bắt đầu
                                    $effectiveStatus = 'scheduled';
                                } elseif ($time >= $s['start_time'] && $time < $s['end_time']) {
                                    // Đang trong khoảng giờ học
                                    $effectiveStatus = 'ongoing';
                                } else {
                                    // Đã qua giờ kết thúc
                                    $effectiveStatus = 'ended';
                                }
                            }
                        ?>

                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['semester_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($s['subject_code']) ?> - <?= htmlspecialchars($s['subject_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($s['session_date'])) ?></td>
                        <td><?= htmlspecialchars($s['start_time']) ?> - <?= htmlspecialchars($s['end_time']) ?></td>
                        <td><?= htmlspecialchars($s['room'] ?? '-') ?></td>
                        <td>
                            <?php if ($s['is_makeup']): ?>
                                <span class="badge badge-warning"><i class="fas fa-sync"></i> Buổi bù</span>
                            <?php else: ?>
                                <span class="badge badge-muted"><i class="fas fa-calendar"></i> Thường</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($effectiveStatus === 'ongoing'): ?>
                                <span class="badge badge-success"><i class="fas fa-play"></i> Đang diễn ra</span>
                            <?php elseif ($effectiveStatus === 'ended'): ?>
                                <span class="badge badge-muted"><i class="fas fa-check"></i> Đã kết thúc</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-calendar"></i> Sắp tới</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['is_attendance_done']): ?>
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Đã</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-circle"></i> Chưa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
