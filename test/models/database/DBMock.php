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

	public function createMockForMethod($method, $query, $values, $return, $db = null) {
		if(is_null($db)) $db = $this->createMock(DB::class);
		
		$db->expects($this->any())
			->method($method)
			->with($query, $values)
			->willReturn($return);
		return $db;
	}

	public function createMockForMethodWithoutEntries($method, $return, $db = null) {
		if(is_null($db)) $db = $this->createMock(DB::class);
		
		$db->expects($this->any())
			->method($method)
			->willReturn($return);
		return $db;
	}


	public static function getDataUser($username): array {
		return [self::getCloneUserData($username)];
	}

	/**
	 * Devuelve una copia de los datos de usuario para que los
	 * datos originales no se vean afectados
	 *
	 * @param [type] $username
	 * @return array
	 */
	public static function getCloneUserData($username): array {
		$dataUser = self::$dataUsers[$username];
		$data = [];
		foreach ($dataUser as $key => $value) {
			$data[$key] = $value;
		}
		return $data;
	}

	public static function getDataUsers(){
		$users = [];
		foreach (self::$dataUsers as $key => $dataUser) {
			$users[] = self::getCloneUserData($key);
		}
		return $users;
	}

	public static function getDataForInsertUpdate($username){
		$dataUser = self::$dataUsers[$username];
		$data = [];

		foreach ($dataUser as $key => $value) {
			$data[':'.$key] = $value;
		}
		return $data;
	}
}