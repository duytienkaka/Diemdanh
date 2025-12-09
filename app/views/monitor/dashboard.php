<div class="dashboard-header">
    <h1><i class="fas fa-chalkboard-user"></i> Bảng Điều Khiển Lớp Trưởng</h1>
    <p class="dashboard-subtitle">Quản lý điểm danh lớp học</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $totalStudents ?? 0 ?></div>
            <div class="stat-label">Tổng sinh viên</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $presentToday ?? 0 ?></div>
            <div class="stat-label">Có mặt hôm nay</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fas fa-user-times"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $absentToday ?? 0 ?></div>
            <div class="stat-label">Vắng hôm nay</div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-section">
        <h2><i class="fas fa-tasks"></i> Điểm danh</h2>
        <div class="action-grid">
            <a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=today" class="action-card">
                <div class="action-icon bg-primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="action-text">
                    <h3>Điểm danh hôm nay</h3>
                    <p>Điểm danh buổi học hôm nay</p>
                </div>
            </a>

            <a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=history" class="action-card">
                <div class="action-icon bg-secondary">
                    <i class="fas fa-history"></i>
                </div>
                <div class="action-text">
                    <h3>Lịch sử điểm danh</h3>
                    <p>Xem lại các buổi đã điểm danh</p>
                </div>
            </a>
        </div>
    </div>
</div>
