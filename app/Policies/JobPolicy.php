<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Job;

class JobPolicy
{
    public function view(Admin $admin, Job $job): bool
    {
        return $admin->id === $job->admin_id;
    }

    public function update(Admin $admin, Job $job): bool
    {
        return $admin->id === $job->admin_id;
    }

    public function delete(Admin $admin, Job $job): bool
    {
        return $admin->id === $job->admin_id;
    }

    public function create(Admin $admin): bool
    {
        return true;
    }

    public function viewAny(Admin $admin): bool
    {
        return true;
    }
}
