# RBAC

Roles and capabilities used by gates and policies:

- super_admin – all access
- admin – manage users, academic, finance
- admin_akademik – academic data and reports
- admin_keuangan – finance, payments, ledger
- operator_ppdb – admissions verification
- guru – attendance, assignments, grading
- wali_kelas – homeroom oversight
- siswa – student self actions

Gates defined (AuthServiceProvider):
- isSuperAdmin, isAdmin, isAdminAkademik, isAdminKeuangan, isOperatorPPDB, isTeacher, isHomeroom, isStudent

