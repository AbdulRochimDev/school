<?php
namespace App\Policies;

use App\Models\{Assignment, User};

class AssignmentPolicy
{
    public function viewAny(User $user){ return true; }
    public function view(User $user, Assignment $model){ return true; }
    public function create(User $user){ return app('gate')->allows('isTeacher') || app('gate')->allows('isAdmin'); }
    public function update(User $user, Assignment $model){ return app('gate')->allows('isTeacher') || app('gate')->allows('isAdmin'); }
    public function delete(User $user, Assignment $model){ return app('gate')->allows('isAdmin'); }
}

