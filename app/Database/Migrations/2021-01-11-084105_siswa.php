<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Siswa extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'nis' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'nama' => [
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null'          => TRUE,
			],
			'id_kelas' => [
				'type' => 'INT',
				'constraint' => 11,
				'null'          => TRUE,
			],
			'photo' => [
				'type' => 'TEXT',
				'null'          => TRUE,
			],
			'password' => [
				'type' => 'VARCHAR',
				'constraint' => 250
			],
		]);
		$this->forge->addKey('nis', true);
		$this->forge->addKey(['id_kelas']);
		$this->forge->createTable('siswa');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		//
	}
}
