# API Surface

Base prefix: `/api`

Teacher (requires `auth:sanctum` + `can:isTeacher`):
- POST `/v1/guru/attendance/sessions` → create session
- POST `/v1/guru/attendance/sessions/{id}/open` → open session
- POST `/v1/guru/attendance/sessions/{id}/close` → close session (dispatch event)
- GET `/v1/guru/attendance/sessions?class_subject_id=&date=` → list sessions
- PUT `/v1/guru/attendance/sessions/{id}/records` → bulk mark attendance
- POST `/v1/guru/assignments` → create assignment
- POST `/v1/guru/assignments/{id}/grade` → bulk grade
- POST `/v1/guru/assignments/{id}/grade-items` → create grade item

Student (requires `auth:sanctum` + `can:isStudent`):
- POST `/v1/siswa/assignments/{id}/submit` → submit with files
- GET `/v1/siswa/assignments/{id}` → view my submission
- GET `/v1/siswa/attendance` → my attendance summary

Health:
- GET `/health` → { status: ok }

