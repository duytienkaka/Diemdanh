<div class="page-header">
    <h1><i class="fas fa-user-graduate"></i> Bảng Điều Khiển Sinh Viên</h1>
    <p class="page-subtitle">Theo dõi lịch học và điểm danh của bạn</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalSubjects ?? 0 ?></div>
            <div class="stat-label">Môn học</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $presentCount ?? 0 ?></div>
            <div class="stat-label">Có mặt</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-user-times"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $absentCount ?? 0 ?></div>
            <div class="stat-label">Vắng</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2><i class="fas fa-calendar-days"></i> Lịch học & Điểm danh</h2>
        <div class="action-grid">
            <a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=schedule" class="action-card">
                <div class="action-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="action-text">
                    <h3>Lịch học</h3>
                    <p>Xem lịch học 7 ngày tới</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=attendanceToday" class="action-card">
                <div class="action-icon bg-secondary">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="action-text">
                    <h3>Điểm danh hôm nay</h3>
                    <p>Kiểm tra trạng thái điểm danh</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=history" class="action-card">
                <div class="action-icon bg-accent">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-text">
                    <h3>Lịch sử điểm danh</h3>
                    <p>Xem lịch sử điểm danh đầy đủ</p>
                </div>
            </a>
        </div>
    </div>
</div>
