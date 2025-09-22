<?php
namespace App\Policies;

use App\Models\{Submission, User};

class SubmissionPolicy
{
    public function viewAny(User $user){ return true; }
    public function view(User $user, Submission $model){ return true; }
    public function create(User $user){ return app('gate')->allows('isStudent'); }
    public function update(User $user, Submission $model){ return app('gate')->allows('isTeacher'); }
    public function delete(User $user, Submission $model){ return app('gate')->allows('isAdmin'); }
}

