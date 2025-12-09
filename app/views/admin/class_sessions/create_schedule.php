<div class="page-header">
    <h1><i class="fas fa-calendar-plus"></i> Tạo thời khóa biểu</h1>
    <p class="page-subtitle">Tạo lịch học định kỳ cho môn học trong học kỳ đã chọn.</p>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <div><i class="fas fa-exclamation-circle"></i> <strong>Lỗi:</strong></div>
        <?php foreach ($errors as $e): ?><p style="margin: 4px 0 0 24px;">• <?= htmlspecialchars($e) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="" class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label>Học kỳ <span style="color: #ff6b6b;">*</span></label>
            <select name="semester_id" id="semester-select" required>
                <option value="">-- Chọn học kỳ --</option>
                <?php foreach ($semesters as $sem): ?>
                    <option value="<?= $sem['id'] ?>">
                        <?= htmlspecialchars($sem['name']) ?>
                        (<?= htmlspecialchars($sem['start_date']) ?> - <?= htmlspecialchars($sem['end_date']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Môn học <span style="color: #ff6b6b;">*</span></label>
            <select name="subject_id" id="subject-select" required>
                <option value="">-- Chọn môn --</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Thứ trong tuần <span style="color: #ff6b6b;">*</span></label>
            <select name="days_of_week[]" required>
                <option value="">-- Chọn --</option>
                <option value="1">Thứ 2</option>
                <option value="2">Thứ 3</option>
                <option value="3">Thứ 4</option>
                <option value="4">Thứ 5</option>
                <option value="5">Thứ 6</option>
                <option value="6">Thứ 7</option>
                <option value="0">Chủ nhật</option>
            </select>
        </div>

        <div class="form-group">
            <label>Phòng học</label>
            <input type="text" name="room" placeholder="Ví dụ: A101">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Từ ngày <span style="color: #ff6b6b;">*</span></label>
            <input type="date" name="start_date" required>
        </div>

        <div class="form-group">
            <label>Đến ngày <span style="color: #ff6b6b;">*</span></label>
            <input type="date" name="end_date" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Giờ bắt đầu <span style="color: #ff6b6b;">*</span></label>
            <input type="time" name="start_time" required>
        </div>

        <div class="form-group">
            <label>Giờ kết thúc <span style="color: #ff6b6b;">*</span></label>
            <input type="time" name="end_time" required>
        </div>
    </div>

    <div class="form-actions" style="display: flex; gap: 8px; margin-top: 24px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Tạo thời khóa biểu</button>
        <a href="<?= BASE_URL ?>index.php?controller=admin_class_session&action=index" class="btn btn-muted"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const semesterSelect = document.getElementById('semester-select');
    const subjectSelect  = document.getElementById('subject-select');

    function loadSubjectsForSemester(semesterId) {
        subjectSelect.innerHTML = '<option value="">-- Chọn môn --</option>';

        if (!semesterId) {
            return;
        }

        const url = '<?= BASE_URL ?>index.php?controller=admin_subject'
                  + '&action=listBySemesterAjax'
                  + '&semester_id=' + encodeURIComponent(semesterId);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">-- Chọn môn --</option>';
                if (!Array.isArray(data) || data.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Không có môn nào trong học kỳ này';
                    subjectSelect.appendChild(opt);
                    return;
                }

                data.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.code + ' - ' + sub.name;
                    subjectSelect.appendChild(opt);
                });
            })
            .catch(err => {
                console.error(err);
                subjectSelect.innerHTML = '<option value="">Lỗi tải danh sách môn</option>';
            });
    }
    semesterSelect.addEventListener('change', function () {
        loadSubjectsForSemester(this.value);
    });
    if (semesterSelect.value) {
        loadSubjectsForSemester(semesterSelect.value);
    }
});
</script>
