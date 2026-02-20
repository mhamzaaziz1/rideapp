<?php

namespace Modules\IAM\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIamTables extends Migration
{
    public function up()
    {
        // Table: users
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'first_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'      => ['type' => 'ENUM', 'constraint' => ['active', 'banned', 'pending'], 'default' => 'active'],
            'avatar'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // Table: roles
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true], 
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles', true);

        // Table: users_roles
        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'role_id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
        ]);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users_roles', true);

        // Table: permissions
        $this->forge->addField([
            'id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'  => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true], // e.g., trip.create
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('permissions', true);

        // Table: roles_permissions
        $this->forge->addField([
            'role_id'       => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('roles_permissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('roles_permissions', true);
        $this->forge->dropTable('permissions', true);
        $this->forge->dropTable('users_roles', true);
        $this->forge->dropTable('roles', true);
        $this->forge->dropTable('users', true);
    }
}
