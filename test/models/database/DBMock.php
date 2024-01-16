<?php
namespace Test\models\database;

use Model\database\DB;
use PHPUnit\Framework\TestCase;

class DBMock extends TestCase {
	public static $dataUsers = [
		'josemi' => [
			'id' => 1,
			'username' => 'josemi',
			'email' => 'josemi@josemi.com',
			'password' => '$2y$10$ISWn21sOxa8Z/qovOFo3L.nvk8CkgYyo7UYhrUr/779vlqNvG2INK',
			'passwordToHash' => 'Josemi123'
		],
		'pacorro' => [
			'id' => 2,
			'username' => 'pacorro',
			'email' => 'pacorro@pacorro.com',
			'password' => '$2y$10$iPe4shZQS5cac2uZQiFV9e4QN3pvznkL3u88r4Q1u9PW7HL90fRPa',
			'passwordToHash' => 'paCorro123456'
		]
	];
	public static $db;

	public function createMockForSelectAssoc($query, $values, $return){
		$db = $this->createMock(DB::class);
		$db->expects($this->any())
			->method('selectAssoc')
			->with($query, $values)
			->willReturn($return);
		return $db;
	}

	public static function getDataUser($username){
		return [self::$dataUsers[$username]];
	}

	public static function getDataUsers(){
		$users = [];
		foreach (self::$dataUsers as $dataUser) {
			$users[] = $dataUser;
		}
		return $users;
	}
}