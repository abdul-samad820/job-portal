<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\JobCategory;
 
class JobCategoryPolicy
{
    public function view(Admin $admin, JobCategory $category): bool
    {
        return $admin->id === $category->admin_id;
    }

    public function update(Admin $admin, JobCategory $category): bool
    {
        return $admin->id === $category->admin_id;
    }

    public function delete(Admin $admin, JobCategory $category): bool
    {
        return $admin->id === $category->admin_id;
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
