-- SQL ADDITIONS — Attendance & Submissions (aligns with existing schema)

CREATE TABLE attendance_sessions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  term_id BIGINT UNSIGNED NULL,
  class_id BIGINT UNSIGNED NULL,
  class_subject_id BIGINT UNSIGNED NULL,
  teacher_id BIGINT UNSIGNED NOT NULL,
  session_date DATE NOT NULL,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  status ENUM('planned','open','closed','cancelled') DEFAULT 'planned',
  topic VARCHAR(191) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_att_term FOREIGN KEY (term_id) REFERENCES terms(id) ON DELETE SET NULL,
  CONSTRAINT fk_att_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
  CONSTRAINT fk_att_cs FOREIGN KEY (class_subject_id) REFERENCES class_subjects(id) ON DELETE SET NULL,
  CONSTRAINT fk_att_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attendance_records (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  attendance_session_id BIGINT UNSIGNED NOT NULL,
  student_id BIGINT UNSIGNED NOT NULL,
  status ENUM('present','late','excused','absent') DEFAULT 'absent',
  checkin_at DATETIME NULL,
  note VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY uq_att_record (attendance_session_id, student_id),
  CONSTRAINT fk_attrec_session FOREIGN KEY (attendance_session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
  CONSTRAINT fk_attrec_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE submission_files (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  submission_id BIGINT UNSIGNED NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NULL,
  size_bytes BIGINT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_subfile_submission FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Helpful indexes
ALTER TABLE attendance_sessions
  ADD INDEX idx_att_sess_date (session_date),
  ADD INDEX idx_att_sess_cs (class_subject_id),
  ADD INDEX idx_att_sess_teacher (teacher_id);
ALTER TABLE attendance_records
  ADD INDEX idx_att_rec_student (student_id);

-- Optional helper table for recurring meetings
CREATE TABLE class_meetings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  class_subject_id BIGINT UNSIGNED NOT NULL,
  weekday TINYINT UNSIGNED NOT NULL,  -- 0=Sun..6=Sat
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  room VARCHAR(50) NULL,
  active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_meet_cs FOREIGN KEY (class_subject_id) REFERENCES class_subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
