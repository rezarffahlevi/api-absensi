<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kelas extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id_kelas' => [
				'type' => 'INT',
				'constraint' => 11,
				'auto_increment' => TRUE
			],
			'kelas' => [
				'type' => 'VARCHAR',
				'constraint' => 250
			],
		]);
		$this->forge->addKey('id_kelas', true);
		$this->forge->createTable('kelas');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
