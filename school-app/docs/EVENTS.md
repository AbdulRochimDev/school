# Domain Events & Listeners

- AttendanceSessionClosed
  - RecomputeAttendanceGrades
  - UpdateReportCards
- SubmissionReceived
  - NotifyTeacherOnSubmission
- GradesUpdated
  - UpdateReportCards
- PaymentVerified
  - CreateLedgerEntriesOnPaymentVerified
- PPDBApplicationVerified
  - ProvisionStudentOnPPDBVerified

These events are registered in `app/Providers/EventServiceProvider.php`.

