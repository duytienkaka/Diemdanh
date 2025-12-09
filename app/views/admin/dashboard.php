<div class="dashboard-header">
    <h1><i class="fas fa-chart-line"></i> Bảng Điều Khiển Admin</h1>
    <p class="dashboard-subtitle">Quản lý toàn bộ hệ thống điểm danh</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-users"></i></div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalStudents ?? 0 ?></div>
            <div class="stat-label">Sinh viên</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-accent"><i class="fas fa-book"></i></div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalSubjects ?? 0 ?></div>
            <div class="stat-label">Môn học</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-secondary"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalSemesters ?? 0 ?></div>
            <div class="stat-label">Học kỳ</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-clock"></i></div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalSessions ?? 0 ?></div>
            <div class="stat-label">Buổi học</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2><i class="fas fa-cog"></i> Quản lý chính</h2>
        <div class="action-grid">
            <a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=index" class="action-card">
                <div class="action-icon bg-primary"><i class="fas fa-calendar-check"></i></div>
                <div class="action-text">
                    <h3>Quản lý Học kỳ</h3>
                    <p>Tạo và chỉnh sửa học kỳ</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=index" class="action-card">
                <div class="action-icon bg-accent"><i class="fas fa-book-open"></i></div>
                <div class="action-text">
                    <h3>Quản lý Môn học</h3>
                    <p>Thêm/sửa thông tin môn học</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=admin_student&action=index" class="action-card">
                <div class="action-icon bg-secondary"><i class="fas fa-user-graduate"></i></div>
                <div class="action-text">
                    <h3>Quản lý Sinh viên</h3>
                    <p>Quản lý thông tin sinh viên</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=index" class="action-card">
                <div class="action-icon bg-info"><i class="fas fa-chalkboard-user"></i></div>
                <div class="action-text">
                    <h3>Danh sách Buổi học</h3>
                    <p>Xem tất cả buổi học</p>
                </div>
            </a>
        </div>
    </div>

    <div class="dashboard-section">
        <h2><i class="fas fa-clock"></i> Tạo lịch trình</h2>
        <div class="action-grid">
            <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createSchedule" class="action-card">
                <div class="action-icon bg-success"><i class="fas fa-plus-circle"></i></div>
                <div class="action-text">
                    <h3>Tạo Thời khóa biểu</h3>
                    <p>Lên lịch học cho môn học</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createMakeup" class="action-card">
                <div class="action-icon bg-warning"><i class="fas fa-sync-alt"></i></div>
                <div class="action-text">
                    <h3>Tạo Buổi học bù</h3>
                    <p>Lên lịch buổi học bù</p>
                </div>
            </a>
        </div>
    </div>

    <div class="dashboard-section">
        <h2><i class="fas fa-chart-bar"></i> Báo cáo & Thống kê</h2>
        <div class="action-grid">
            <a href="<?= BASE_URL ?>index.php?controller=admin_report&action=index" class="action-card">
                <div class="action-icon bg-danger"><i class="fas fa-file-chart-line"></i></div>
                <div class="action-text">
                    <h3>Thống kê Vắng học</h3>
                    <p>Xem báo cáo vắng học</p>
                </div>
            </a>
        </div>
    </div>

    <div class="dashboard-section">
        <h2><i class="fas fa-calendar-day"></i> Buổi học hôm nay</h2>
        <?php if (empty($todaySessions)): ?>
            <div class="empty-state" style="padding: 40px; text-align: center; background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(0, 212, 255, 0.05) 100%); border-radius: 12px; border: 1px dashed #e5e7eb;">
                <div class="empty-icon" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 style="color: #6b7280; margin: 0; font-size: 16px;">Hôm nay không có buổi học nào</h3>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Môn học</th>
                            <th>Phòng</th>
                            <th>Học kỳ</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($todaySessions as $session): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($session['start_time']) ?> - <?= htmlspecialchars($session['end_time']) ?></strong></td>
                            <td><?= htmlspecialchars($session['subject_code'] ?? '-') ?> - <?= htmlspecialchars($session['subject_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($session['room'] ?? 'Không xác định') ?></td>
                            <td>
                                <?php 
                                // Debug: In ra tên học kỳ
                                $semesterName = $session['semester_name'] ?? null;
                                if ($semesterName) {
                                    echo htmlspecialchars($semesterName);
                                } else {
                                    // Nếu không có, lấy từ semester_id
                                    $semesterId = $session['semester_id'] ?? null;
                                    if ($semesterId) {
                                        $sem = \Semester::findById($semesterId);
                                        echo htmlspecialchars($sem['name'] ?? 'N/A');
                                    } else {
                                        echo 'N/A';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($session['is_makeup']): ?>
                                    <span class="badge badge-warning"><i class="fas fa-sync"></i> Buổi bù</span>
                                <?php else: ?>
                                    <span class="badge badge-muted"><i class="fas fa-calendar"></i> Thường</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($session['status'] === 'ended'): ?>
                                    <span class="badge badge-muted"><i class="fas fa-check"></i> Đã kết thúc</span>
                                <?php elseif ($session['status'] === 'ongoing'): ?>
                                    <span class="badge badge-success"><i class="fas fa-play"></i> Đang diễn ra</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Sắp tới</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>