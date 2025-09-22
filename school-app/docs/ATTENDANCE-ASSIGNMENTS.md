# Attendance, Assignment Submission, and Class Mechanisms (Aligned with Existing Schema)

This module extends the earlier schema with **attendance**, **richer submissions**, and **class session planning**. 
It integrates with: `classes`, `class_subjects`, `students`, `assignments`, `submissions`, `grade_items`, `grades`, and `report_card_items`.

---

## A. Attendance

### New Tables

1) **attendance_sessions**
- Links an instructional session to a `class_subject` (or `class`) and a `term`.
- Created by a **teacher** (or scheduled by admin), then opened/closed to collect attendance.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| term_id | BIGINT FK -> `terms.id` | nullable if school doesn't use terms per session |
| class_id | BIGINT FK -> `classes.id` | for homeroom sessions |
| class_subject_id | BIGINT FK -> `class_subjects.id` | for subject-based lessons |
| teacher_id | BIGINT FK -> `teachers.id` | owner |
| session_date | DATE | |
| starts_at | DATETIME NULL | planned start |
| ends_at | DATETIME NULL | planned end |
| status | ENUM('planned','open','closed','cancelled') | |
| topic | VARCHAR(191) NULL | optional topic |
| created_at/updated_at | TIMESTAMP | |

2) **attendance_records**
- One record per **student per session**.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| attendance_session_id | BIGINT FK -> `attendance_sessions.id` | |
| student_id | BIGINT FK -> `students.id` | |
| status | ENUM('present','late','excused','absent') | default 'absent' |
| checkin_at | DATETIME NULL | when marked present/late |
| note | VARCHAR(255) NULL | reason/remark |
| created_at/updated_at | TIMESTAMP | |
| UNIQUE (attendance_session_id, student_id) | |

### Attendance → Grading

- Each `class_subject` can have a **GradeItem** named e.g. `"Attendance"` with `weight` (e.g., 10%) and `max_score=100`.
- Score per student is computed as:  
  `attendance_score = 100 * (present + late*0.5 + excused*0.75) / (total_sessions)` (tune via settings)  
- The system writes/updates a `grades` row for that `grade_item_id` per student.
- These then roll up into `report_card_items` together with other assessments.

### Workflow

1) Teacher creates session (planned) → opens session at class time.  
2) Teacher marks each enrolled student as present/late/excused/absent (bulk and inline).  
3) On close, an **event** `AttendanceSessionClosed` recomputes attendance_grade for that class_subject and writes to `grades`.  
4) `GradesUpdated` updates `report_card_items` for the term.

---

## B. Assignment Submission Extensions

### New Table: submission_files

For multiple attachments per submission and storage-friendly metadata.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| submission_id | BIGINT FK -> `submissions.id` | |
| file_path | VARCHAR(255) NOT NULL | relative path or URL (Cloud/R2) |
| mime_type | VARCHAR(100) NULL | |
| size_bytes | BIGINT NULL | |
| created_at/updated_at | TIMESTAMP | |

> Existing `submissions.content` may store essay/URL; use `submission_files` for binary uploads.

### Teacher Grading

- Teachers can either grade via `submissions.score` & `feedback`, or create **GradeItems** for finer breakdown (e.g., rubric).  
- If a rubric is used, store detail in `grades` linked to rubric-type `grade_items` (e.g., *Content*, *Format*).

---

## C. Class Session Planning

Optional helper table for recurring plans:

**class_meetings**  
- `id, class_subject_id, weekday TINYINT(0..6), start_time, end_time, room, active`  
- From these, generate `attendance_sessions` automatically for a date range (cron/command).

---

## D. API Surface (Additions)

### Teacher
- `POST /v1/guru/attendance/sessions` → create planned session
- `POST /v1/guru/attendance/sessions/{id}/open` → open
- `POST /v1/guru/attendance/sessions/{id}/close` → close & recompute grades
- `GET /v1/guru/attendance/sessions?class_subject_id=&date=` → list
- `PUT /v1/guru/attendance/sessions/{id}/records` (bulk) → mark statuses for students

- `POST /v1/guru/assignments` → create
- `POST /v1/guru/assignments/{id}/grade` → grade submission(s)
- `POST /v1/guru/assignments/{id}/grade-items` → create grade items (rubric)
- `POST /v1/guru/grades/bulk` → bulk store grades

### Student
- `GET /v1/siswa/attendance` → my attendance summary
- `POST /v1/siswa/assignments/{id}/submit` → submit with files
- `GET /v1/siswa/assignments/{id}` → detail & status

---

## E. Events & Jobs

- **AttendanceSessionClosed** → handler recompute attendance GradeItem & upsert `grades`.  
- **SubmissionReceived** → notify teacher, optional plagiarism job.  
- **GradesUpdated** → recalc `report_card_items` (weighted mean by `grade_items.weight`).

---

## F. Tests (Feature)

- Create session → open → mark → close → assert grade written.  
- Submit assignment (+file) → grade by teacher → assert grade stored & report recomputed.

---

## G. Settings (Tuning)

`settings` keys (examples):
- `attendance.late_weight = 0.5`
- `attendance.excused_weight = 0.75`
- `attendance.default_weight = 10`
- `attendance.auto_grade_item = true`

---

**Everything here is consistent with earlier schema; only **adds** tables and a new flow that feeds back into the existing grading → report pipeline.**
