<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Assignment::class => \App\Policies\AssignmentPolicy::class,
        \App\Models\Submission::class => \App\Policies\SubmissionPolicy::class,
        \App\Models\AttendanceSession::class => \App\Policies\AttendanceSessionPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        $roles = [
            'isSuperAdmin' => 'super_admin',
            'isAdmin' => 'admin',
            'isAdminAkademik' => 'admin_akademik',
            'isAdminKeuangan' => 'admin_keuangan',
            'isOperatorPPDB' => 'operator_ppdb',
            'isTeacher' => 'guru',
            'isHomeroom' => 'wali_kelas',
            'isStudent' => 'siswa',
        ];
        foreach ($roles as $ability => $role) {
            Gate::define($ability, function ($user) use ($role) {
                return self::userHasRole($user, $role);
            });
        }

        Gate::before(function ($user, $ability) {
            if (self::userHasRole($user, 'super_admin')) return true;
        });
    }

    protected static function userHasRole($user, string $roleSlug): bool
    {
        try {
            if (method_exists($user, 'hasRole')) {
                return (bool) $user->hasRole($roleSlug);
            }
            if (method_exists($user, 'roles')) {
                $rel = $user->roles();
                $exists = $rel->where('slug', $roleSlug)->exists();
                if ($exists) return true;
                // If relation already loaded
                if ($user->relationLoaded('roles')) {
                    return collect($user->getRelation('roles'))->pluck('slug')->contains($roleSlug);
                }
            }
            // Common fallbacks
            if (property_exists($user, 'role') && is_string($user->role)) {
                return $user->role === $roleSlug;
            }
            if (isset($user->attributes['role'])) {
                return $user->attributes['role'] === $roleSlug;
            }
        } catch (\Throwable $e) {
            // be permissive only for student self-scoped abilities
        }
        return false;
    }
}

