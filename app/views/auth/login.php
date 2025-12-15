<div class="login-split-container">
    <!-- Left side: Branding & Info -->
    <div class="login-left">
        <div class="login-left-content">
            <div class="login-branding">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>Attendance System</h1>
                <p>Hệ thống quản lý điểm danh thông minh</p>
            </div>
            
            <div class="login-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Điểm danh nhanh chóng</h3>
                        <p>Quản lý điểm danh theo thời gian thực</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Báo cáo chi tiết</h3>
                        <p>Xem thống kê và báo cáo toàn diện</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="feature-text">
                        <h3>An toàn & Bảo mật</h3>
                        <p>Dữ liệu được bảo vệ an toàn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side: Login Form -->
    <div class="login-right">
        <div class="login-form-wrapper">
            <div class="login-form-header">
                <h2>Đăng nhập</h2>
                <p>Sử dụng tài khoản của bạn để truy cập</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-exclamation-circle" style="margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <?php foreach ($errors as $e): ?>
                                <p style="margin: 4px 0; font-size: 14px;"><?= htmlspecialchars($e ?? '') ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Tài khoản
                    </label>
                    <input type="text" id="username" name="username" 
                           placeholder="Nhập tài khoản của bạn"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" 
                           placeholder="Nhập mật khẩu của bạn"
                           required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px;">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            <div class="login-footer">
                <p><i class="fas fa-info-circle"></i> © 2025 Attendance App</p>
            </div>
        </div>
    </div>
</div>
