<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Absensi extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'auto_increment' => TRUE
			],
			'tgl' => [
				'type'          => 'TIMESTAMP',
				'null'          => TRUE,
			],
			'nis' => [
				'type' => 'VARCHAR',
				'constraint' => 50
			],
			'photo' => [
				'type' => 'TEXT',
			],
			'latitude' => [
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null'          => TRUE,
			],
			'longitude' => [
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null'          => TRUE,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addKey(['nis']);
		$this->forge->createTable('absensi');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
