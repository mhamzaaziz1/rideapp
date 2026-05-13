<?php

/**
 * IAM Permission Helper Functions
 * 
 * Global helper functions available throughout the system for
 * checking permissions in views, controllers, and services.
 * 
 * Usage examples:
 *   if (can('dispatch.trips.create'))    { ... }
 *   if (has_permission('fleet.drivers.delete')) { ... }
 *   if (is_admin())                      { ... }
 */

if (!function_exists('can')) {
    /**
     * Check if the current logged-in user has a specific permission
     * Shorthand for has_permission()
     *
     * @param string $permission Permission slug (e.g., 'dispatch.trips.create')
     * @return bool
     */
    function can(string $permission): bool
    {
        static $service = null;
        if ($service === null) {
            $service = new \Modules\IAM\Services\PermissionService();
        }
        return $service->currentUserCan($permission);
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if the current logged-in user has a specific permission
     *
     * @param string $permission Permission slug (e.g., 'dispatch.trips.create')
     * @return bool
     */
    function has_permission(string $permission): bool
    {
        return can($permission);
    }
}

if (!function_exists('can_any')) {
    /**
     * Check if the current logged-in user has ANY of the given permissions
     *
     * @param array $permissions Array of permission slugs
     * @return bool
     */
    function can_any(array $permissions): bool
    {
        static $service = null;
        if ($service === null) {
            $service = new \Modules\IAM\Services\PermissionService();
        }
        return $service->currentUserCanAny($permissions);
    }
}

if (!function_exists('can_all')) {
    /**
     * Check if the current logged-in user has ALL of the given permissions
     *
     * @param array $permissions Array of permission slugs
     * @return bool
     */
    function can_all(array $permissions): bool
    {
        static $service = null;
        if ($service === null) {
            $service = new \Modules\IAM\Services\PermissionService();
        }
        return $service->currentUserCanAll($permissions);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if the current user has the Admin role
     *
     * @return bool
     */
    function is_admin(): bool
    {
        static $service = null;
        if ($service === null) {
            $service = new \Modules\IAM\Services\PermissionService();
        }
        return $service->isAdmin();
    }
}

if (!function_exists('current_user_role')) {
    /**
     * Get the current user's role name
     *
     * @return string|null
     */
    function current_user_role(): ?string
    {
        static $service = null;
        if ($service === null) {
            $service = new \Modules\IAM\Services\PermissionService();
        }
        return $service->getCurrentUserRole();
    }
}

if (!function_exists('abort_no_permission')) {
    /**
     * Abort with a 403 response if user lacks the given permission
     *
     * @param string $permission
     * @return void
     */
    function abort_no_permission(string $permission): void
    {
        if (!can($permission)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'You do not have permission to access this resource.'
            );
        }
    }
}
