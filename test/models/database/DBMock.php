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
			'password' => 'ccc'
		],
		'pacorro' => [
			'id' => 2,
			'username' => 'pacorro',
			'email' => 'pacorro@pacorro.com',
			'password' => '123456'
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
}