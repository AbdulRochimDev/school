<?php
namespace App\Policies;

use App\Models\{AttendanceSession, User};

class AttendanceSessionPolicy
{
    public function viewAny(User $user){ return app('gate')->allows('isTeacher') || app('gate')->allows('isAdmin'); }
    public function view(User $user, AttendanceSession $model){ return app('gate')->allows('isTeacher') || app('gate')->allows('isAdmin'); }
    public function create(User $user){ return app('gate')->allows('isTeacher'); }
    public function update(User $user, AttendanceSession $model){ return app('gate')->allows('isTeacher') || app('gate')->allows('isAdmin'); }
    public function delete(User $user, AttendanceSession $model){ return app('gate')->allows('isAdmin'); }
}

