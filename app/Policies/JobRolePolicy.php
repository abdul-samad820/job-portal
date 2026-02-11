<?php

namespace App\Policies;

use App\Models\JobRole;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class JobRolePolicy
{
    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function view(Admin $admin, JobRole $Role): bool
    {
        return $admin->id === $Role->admin_id;
    }


    public function create(Admin $admin): bool
    {
        return true;
    }

    public function update(Admin $admin, JobRole $Role): bool
    {
        return $admin->id === $Role->admin_id;
    }

    public function delete(Admin $admin, JobRole $Role): bool
    {
        return $admin->id === $Role->admin_id;
    }

    public function restore(Admin $admin, JobRole $Role): bool
    {
        return false;
    }

    public function forceDelete(Admin $admin, JobRole $Role): bool
    {
        return false;
    }
}
