<?php
$role     = $_SESSION['role']     ?? 'guest';
$username = $_SESSION['username'] ?? 'Khách';
$currentController = $_GET['controller'] ?? '';
$currentAction = $_GET['action'] ?? '';

function nav_active($controller, $action = null)
{
    $c = $GLOBALS['currentController'] ?? '';
    $a = $GLOBALS['currentAction'] ?? '';
    if ($action === null) {
        return $c === $controller ? 'active' : '';
    }
    return ($c === $controller && $a === $action) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Attendance App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/theme-edu.css?v=<?= time() % 100000 ?>"
</head>
<body class="role-<?= htmlspecialchars($role) ?>">

<a href="#main-content" class="sr-only">Bỏ qua sang nội dung</a>

<div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <div class="sidebar-title">Attendance</div>
                <div class="sidebar-role">
                    <?php if ($role === 'admin'): ?>
                        <i class="fas fa-user-tie"></i> Admin
                    <?php elseif ($role === 'monitor'): ?>
                        <i class="fas fa-users"></i> Lớp trưởng
                    <?php elseif ($role === 'student'): ?>
                        <i class="fas fa-user-graduate"></i> Sinh viên
                    <?php else: ?>
                        Khách
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Admin</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_dashboard&action=index" class="<?= nav_active('admin_dashboard','index') ?>"><i class="fas fa-chart-line"></i> <span>Tổng quan</span></a></li>
                    </ul>
                </div>

                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Học kỳ & môn học</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_semester&action=index" class="<?= nav_active('admin_semester','index') ?>"><i class="fas fa-calendar-alt"></i> <span>Quản lý học kỳ</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_subject&action=index" class="<?= nav_active('admin_subject','index') ?>"><i class="fas fa-book-open"></i> <span>Quản lý môn học</span></a></li>
                    </ul>
                </div>

                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Buổi học</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createSchedule" class="<?= nav_active('admin_class_session','createSchedule') ?>"><i class="fas fa-clock"></i> <span>Tạo thời khóa biểu</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=createMakeup" class="<?= nav_active('admin_class_session','createMakeup') ?>"><i class="fas fa-sync-alt"></i> <span>Tạo buổi học bù</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=index" class="<?= nav_active('admin_class_session','index') ?>"><i class="fas fa-list"></i> <span>Danh sách buổi học</span></a></li>
                    </ul>
                </div>

                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Sinh viên & thống kê</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_student&action=index" class="<?= nav_active('admin_student','index') ?>"><i class="fas fa-user-graduate"></i> <span>Quản lý sinh viên</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=admin_report&action=index" class="<?= nav_active('admin_report','index') ?>"><i class="fas fa-chart-bar"></i> <span>Thống kê vắng học</span></a></li>
                    </ul>
                </div>

            <?php elseif ($role === 'monitor'): ?>
                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Lớp trưởng</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=monitor_dashboard&action=index" class="<?= nav_active('monitor_dashboard','index') ?>"><i class="fas fa-home"></i> <span>Tổng quan</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=today" class="<?= nav_active('monitor_attendance','today') ?>"><i class="fas fa-calendar-check"></i> <span>Điểm danh hôm nay</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=monitor_attendance&action=history" class="<?= nav_active('monitor_attendance','history') ?>"><i class="fas fa-history"></i> <span>Lịch sử buổi điểm danh</span></a></li>
                    </ul>
                </div>

            <?php elseif ($role === 'student'): ?>
                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Sinh viên</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=index" class="<?= nav_active('student_dashboard','index') ?>"><i class="fas fa-home"></i> <span>Tổng quan</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=schedule" class="<?= nav_active('student_dashboard','schedule') ?>"><i class="fas fa-book"></i> <span>Lịch học</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=attendanceToday" class="<?= nav_active('student_dashboard','attendanceToday') ?>"><i class="fas fa-check-circle"></i> <span>Điểm danh hôm nay</span></a></li>
                        <li><a href="<?= BASE_URL ?>index.php?controller=student_dashboard&action=history" class="<?= nav_active('student_dashboard','history') ?>"><i class="fas fa-calendar-check"></i> <span>Lịch sử điểm danh</span></a></li>
                    </ul>
                </div>

            <?php else: ?>
                <div class="sidebar-nav-section">
                    <div class="sidebar-nav-section-title">Chung</div>
                    <ul>
                        <li><a href="<?= BASE_URL ?>index.php?controller=auth&action=login"><i class="fas fa-sign-in-alt"></i> <span>Đăng nhập</span></a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main" role="main" id="main-content">
        <header class="topbar">
            <div class="topbar-title">
                <button class="sidebar-toggle" aria-label="Thu / Mở sidebar" title="Thu / Mở sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <i class="fas fa-book-open"></i>
                Hệ thống quản lý điểm danh
            </div>
            <div class="topbar-user">
                <span class="user-name">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?>
                </span>
                <?php if ($role !== 'guest'): ?>
                    <a href="<?= BASE_URL ?>index.php?controller=auth&action=logout" class="btn btn-danger btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                <?php endif; ?>
            </div>
        </header>
        <div class="content container">
            <main class="card">
                <?php
                if (isset($viewFile)) {
                    include $viewFile;
                }
                ?>
            </main>
        </div>
    </main>
</div>

</body>
</html>

<script>
// Sidebar toggle: trên mobile sẽ bật lớp 'sidebar-open'; trên desktop sẽ bật 'sidebar-collapsed'
(function(){
    function isMobile() { return window.innerWidth < 900; }
    var btn = document.querySelector('.sidebar-toggle');
    if (!btn) return;
    btn.addEventListener('click', function(e){
        if (isMobile()) {
            document.body.classList.toggle('sidebar-open');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
        }
    });

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(ev){
        if (!isMobile()) return;
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        var target = ev.target;
        if (document.body.classList.contains('sidebar-open') && !sidebar.contains(target) && !btn.contains(target)) {
            document.body.classList.remove('sidebar-open');
        }
    });
})();
</script>
