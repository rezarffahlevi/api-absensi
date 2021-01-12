<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_user' => [
				'type' => 'INT',
				'constraint' => 11,
				'auto_increment' => TRUE
			],
			'nama' => [
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null'          => TRUE,
			],
			'username' => [
				'type' => 'VARCHAR',
				'constraint' => 250
			],
			'level' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null'          => TRUE,
			],
			'password' => [
				'type' => 'VARCHAR',
				'constraint' => 250
			],
		]);
		$this->forge->addKey('id_user', true);
		$this->forge->createTable('user');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
