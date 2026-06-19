<?php

namespace App\Modules\IAM\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhancePermissionsSystem extends Migration
{
    public function up()
    {
        // 1. Add columns to 'permissions' table
        $this->forge->addColumn('permissions', [
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'name',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'module',
            ],
            'group_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'description',
            ],
        ]);

        // 2. Add columns to 'roles' table
        $this->forge->addColumn('roles', [
            'is_system' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'description',
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'is_system',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_default',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ]);

        // 3. Create user_permissions table for direct user-level overrides
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'granted'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1], // 1=grant, 0=deny override
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_permissions', true);

        // 4. Create permission_audit_log for tracking changes
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'actor_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'action'     => ['type' => 'VARCHAR', 'constraint' => 50], // e.g., 'role_created', 'permission_assigned', 'permission_revoked'
            'target_type'=> ['type' => 'VARCHAR', 'constraint' => 20], // 'role' or 'user'
            'target_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'details'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('permission_audit_log', true);
    }

    public function down()
    {
        $this->forge->dropTable('permission_audit_log', true);
        $this->forge->dropTable('user_permissions', true);

        $this->forge->dropColumn('roles', ['is_system', 'is_default', 'created_at', 'updated_at']);
        $this->forge->dropColumn('permissions', ['module', 'description', 'group_name']);
    }
}
