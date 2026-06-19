<?php

namespace App\Modules\IAM\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds all CRUD permissions for every module/resource in the system.
 * Each permission follows the convention: module.resource.action
 * e.g., dispatch.trips.create, fleet.drivers.delete
 */
class PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // =========================================================
        // Define ALL system permissions grouped by module
        // =========================================================
        $permissionMap = [

            // ---- Dashboard Module ----
            'Dashboard' => [
                'dashboard' => [
                    'view' => 'View main dashboard & analytics',
                ],
            ],

            // ---- Dispatch Module ----
            'Dispatch' => [
                'trips' => [
                    'view'   => 'View trip listings',
                    'create' => 'Create new trips',
                    'edit'   => 'Edit existing trips',
                    'delete' => 'Delete trips',
                    'print'  => 'Print trip receipts & statements',
                    'export' => 'Export trip data',
                    'update_status' => 'Update trip status',
                ],
                'dispatch_board' => [
                    'view'   => 'View dispatch board / live map',
                ],
                'disputes' => [
                    'view'   => 'View disputes',
                    'create' => 'Create disputes',
                    'edit'   => 'Edit dispute details',
                    'delete' => 'Delete disputes',
                    'settle' => 'Settle dispute fares',
                    'comment'=> 'Add comments on disputes',
                ],
                'ratings' => [
                    'view'   => 'View ratings list',
                    'create' => 'Submit ratings',
                ],
                'communications' => [
                    'view' => 'View communications log',
                ],
            ],

            // ---- Fleet Module ----
            'Fleet' => [
                'drivers' => [
                    'view'       => 'View driver listings',
                    'create'     => 'Create new drivers',
                    'edit'       => 'Edit driver details',
                    'delete'     => 'Delete drivers',
                    'profile'    => 'View driver profiles',
                    'add_fund'   => 'Add funds to driver wallet',
                    'update_rate'=> 'Update driver commission rates',
                    'manage_bank'=> 'Manage driver bank accounts',
                    'doc_status' => 'Update driver document status',
                    'print'      => 'Print driver cheques & statements',
                    'export'     => 'Export driver statements',
                ],
            ],

            // ---- Customer Module ----
            'Customer' => [
                'customers' => [
                    'view'       => 'View customer listings',
                    'create'     => 'Create new customers',
                    'edit'       => 'Edit customer details',
                    'delete'     => 'Delete customers',
                    'profile'    => 'View customer profiles',
                    'add_fund'   => 'Add funds to customer wallet',
                    'print'      => 'Print customer statements',
                    'export'     => 'Export customer statements',
                ],
                'addresses' => [
                    'view'   => 'View customer addresses',
                    'create' => 'Create customer addresses',
                    'edit'   => 'Edit customer addresses',
                    'delete' => 'Delete customer addresses',
                ],
                'cards' => [
                    'view'   => 'View customer cards',
                    'create' => 'Create customer cards',
                    'delete' => 'Delete customer cards',
                ],
            ],

            // ---- Billing Module ----
            'Billing' => [
                'finance' => [
                    'view'   => 'View financial dashboard',
                    'print'  => 'Print financial reports',
                    'export' => 'Export CSV financial data',
                ],
                'payouts' => [
                    'view'     => 'View payout listings',
                    'request'  => 'Request new payouts',
                    'complete' => 'Complete/approve payouts',
                    'cancel'   => 'Cancel payouts',
                ],
            ],

            // ---- Pricing Module ----
            'Pricing' => [
                'pricing' => [
                    'view'   => 'View pricing rules',
                    'edit'   => 'Edit pricing rules',
                ],
                'peak_hours' => [
                    'create' => 'Create peak hour rules',
                    'delete' => 'Delete peak hour rules',
                ],
                'zones' => [
                    'create' => 'Create pricing zones',
                    'delete' => 'Delete pricing zones',
                ],
            ],

            // ---- Call Center Module ----
            'CallCenter' => [
                'call_logs' => [
                    'view'   => 'View call log listings',
                    'create' => 'Create call log entries',
                    'edit'   => 'Edit call log entries',
                    'delete' => 'Delete call log entries',
                ],
            ],

            // ---- Setting Module ----
            'Setting' => [
                'settings' => [
                    'view'   => 'View system settings',
                    'edit'   => 'Edit system settings',
                ],
            ],

            // ---- IAM Module (Permissions management itself) ----
            'IAM' => [
                'staff' => [
                    'view'   => 'View staff listings',
                    'create' => 'Create staff members',
                    'edit'   => 'Edit staff members',
                    'delete' => 'Delete staff members',
                ],
                'roles' => [
                    'view'   => 'View roles',
                    'create' => 'Create roles',
                    'edit'   => 'Edit roles & assign permissions',
                    'delete' => 'Delete roles',
                ],
                'permissions' => [
                    'view'   => 'View system permissions',
                    'assign' => 'Assign permissions to roles/users',
                    'revoke' => 'Revoke permissions from roles/users',
                ],
            ],

            // ---- Support Module ----
            'Support' => [
                'chat' => [
                    'view'   => 'View support chat dashboard',
                    'reply'  => 'Reply to support chats',
                    'delete' => 'Delete chat history',
                ],
                'faq' => [
                    'view'   => 'View FAQ entries',
                    'create' => 'Create FAQ entries',
                    'edit'   => 'Edit FAQ entries',
                    'delete' => 'Delete FAQ entries',
                ],
            ],
        ];

        // =========================================================
        // Insert Permissions
        // =========================================================
        foreach ($permissionMap as $module => $resources) {
            foreach ($resources as $resource => $actions) {
                foreach ($actions as $action => $description) {
                    $permName = strtolower($module) . '.' . $resource . '.' . $action;

                    $exists = $db->table('permissions')
                                 ->where('name', $permName)
                                 ->countAllResults();

                    if ($exists == 0) {
                        $db->table('permissions')->insert([
                            'name'        => $permName,
                            'module'      => $module,
                            'description' => $description,
                            'group_name'  => ucfirst(str_replace('_', ' ', $resource)),
                        ]);
                    }
                }
            }
        }

        // =========================================================
        // Mark existing roles as system roles
        // =========================================================
        $db->table('roles')
           ->where('name', 'Admin')
           ->update(['is_system' => 1]);

        // =========================================================
        // Assign ALL permissions to Admin role
        // =========================================================
        $adminRole = $db->table('roles')->where('name', 'Admin')->get()->getRow();
        if ($adminRole) {
            $allPerms = $db->table('permissions')->get()->getResult();
            foreach ($allPerms as $perm) {
                $exists = $db->table('roles_permissions')
                             ->where('role_id', $adminRole->id)
                             ->where('permission_id', $perm->id)
                             ->countAllResults();
                if ($exists == 0) {
                    $db->table('roles_permissions')->insert([
                        'role_id'       => $adminRole->id,
                        'permission_id' => $perm->id,
                    ]);
                }
            }
        }

        // =========================================================
        // Assign relevant permissions to Dispatcher role
        // =========================================================
        $dispatcherRole = $db->table('roles')->where('name', 'Dispatcher')->get()->getRow();
        if ($dispatcherRole) {
            $dispatcherPerms = [
                'dashboard.dashboard.view',
                'dispatch.trips.view', 'dispatch.trips.create', 'dispatch.trips.edit', 'dispatch.trips.print', 'dispatch.trips.update_status',
                'dispatch.dispatch_board.view',
                'dispatch.disputes.view', 'dispatch.disputes.comment',
                'dispatch.ratings.view', 'dispatch.ratings.create',
                'dispatch.communications.view',
                'fleet.drivers.view', 'fleet.drivers.profile',
                'customer.customers.view', 'customer.customers.create', 'customer.customers.profile',
                'customer.addresses.view', 'customer.addresses.create',
                'callcenter.call_logs.view', 'callcenter.call_logs.create',
                'support.chat.view',
            ];
            foreach ($dispatcherPerms as $permName) {
                $perm = $db->table('permissions')->where('name', $permName)->get()->getRow();
                if ($perm) {
                    $exists = $db->table('roles_permissions')
                                 ->where('role_id', $dispatcherRole->id)
                                 ->where('permission_id', $perm->id)
                                 ->countAllResults();
                    if ($exists == 0) {
                        $db->table('roles_permissions')->insert([
                            'role_id'       => $dispatcherRole->id,
                            'permission_id' => $perm->id,
                        ]);
                    }
                }
            }
        }

        // =========================================================
        // Assign relevant permissions to Finance role
        // =========================================================
        $financeRole = $db->table('roles')->where('name', 'Finance')->get()->getRow();
        if ($financeRole) {
            $financePerms = [
                'dashboard.dashboard.view',
                'dispatch.trips.view', 'dispatch.trips.print', 'dispatch.trips.export',
                'billing.finance.view', 'billing.finance.print', 'billing.finance.export',
                'billing.payouts.view', 'billing.payouts.request', 'billing.payouts.complete', 'billing.payouts.cancel',
                'fleet.drivers.view', 'fleet.drivers.profile', 'fleet.drivers.print', 'fleet.drivers.export',
                'customer.customers.view', 'customer.customers.profile', 'customer.customers.print', 'customer.customers.export',
                'pricing.pricing.view',
            ];
            foreach ($financePerms as $permName) {
                $perm = $db->table('permissions')->where('name', $permName)->get()->getRow();
                if ($perm) {
                    $exists = $db->table('roles_permissions')
                                 ->where('role_id', $financeRole->id)
                                 ->where('permission_id', $perm->id)
                                 ->countAllResults();
                    if ($exists == 0) {
                        $db->table('roles_permissions')->insert([
                            'role_id'       => $financeRole->id,
                            'permission_id' => $perm->id,
                        ]);
                    }
                }
            }
        }

        // =========================================================
        // Assign relevant permissions to Driver Manager role
        // =========================================================
        $driverMgrRole = $db->table('roles')->where('name', 'Driver Manager')->get()->getRow();
        if ($driverMgrRole) {
            $driverMgrPerms = [
                'dashboard.dashboard.view',
                'fleet.drivers.view', 'fleet.drivers.create', 'fleet.drivers.edit', 'fleet.drivers.profile',
                'fleet.drivers.doc_status', 'fleet.drivers.manage_bank', 'fleet.drivers.add_fund',
                'fleet.drivers.print', 'fleet.drivers.export',
                'dispatch.trips.view',
                'customer.customers.view',
            ];
            foreach ($driverMgrPerms as $permName) {
                $perm = $db->table('permissions')->where('name', $permName)->get()->getRow();
                if ($perm) {
                    $exists = $db->table('roles_permissions')
                                 ->where('role_id', $driverMgrRole->id)
                                 ->where('permission_id', $perm->id)
                                 ->countAllResults();
                    if ($exists == 0) {
                        $db->table('roles_permissions')->insert([
                            'role_id'       => $driverMgrRole->id,
                            'permission_id' => $perm->id,
                        ]);
                    }
                }
            }
        }

        echo "✅ All permissions seeded and assigned to roles successfully.\n";
    }
}
