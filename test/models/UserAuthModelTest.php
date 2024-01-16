<?php
namespace Test\models;

require_once __DIR__."/../../src/config/app.php";

use Model\database\DB;
use Model\UserAuthModel;
use PHPUnit\Framework\TestCase;
use Test\models\database\DBMock;

class UserAuthModelPrivateToPublic extends UserAuthModel {
	public static function isNumber($value) : bool {
		return parent::isNumber($value);
	}

	public static function isEmail($value) : bool {
		return parent::isEmail($value);
	}

	public function update(): bool {
		return parent::update();
	}

	public function insert(): bool {
		return parent::insert();
	}
}
class UserAuthModelTest extends DBMock {
	public $user;
	public function setUp(): void {
		$datos = [
			'username' => 'Pepe',
			'email' => 'pepe@pepeillo.com',
			'password' => 'yomismo'
		];
		$this->user = new UserAuthModel($datos);
	}
	public function testRegister() {
		$user = new UserAuthModel();
		$registrado = $user->register('Pepe', 'pepe@pepeillo.com', 'yomismo');
		$this->assertTrue($registrado);
	}

	/**
	 * Undocumented function
	 *
	 * @dataProvider passwordsToHash
	 */
	public function testHassPassword($password){
		$passwordHashed = UserAuthModel::hashPassword($password);
		$this->assertIsString($passwordHashed);
		$this->assertTrue(!empty($passwordHashed));
	}

	public static function passwordsToHash(){
		return [
			['Josemi123'],
			['paCorro123456']
		];
	}

	public function testValidate() {
		$validated = $this->user->validate();
		$this->assertTrue($validated);
	}

	/**
	 * Función que testea propiedades vacías
	 *
	 * @param [type] $property
	 * @param [type] $value
	 * @return void
	 * @dataProvider emptyProperties
	 */
	public function testValidateWithEmptyProperty($property, $value){
		$user = clone $this->user; 
		$user->$property = $value;
		$validated = $user->validate();
		$this->assertFalse($validated);
	}

	public static function emptyProperties(){
		return [
			['username', ''],
			['username', '   '],
			['email', ''],
			['email', '  '],
			['password', ''],
			['password', '  ']
		];
	}

	/**
	 * Función que testea propiedades vacías
	 *
	 * @param [type] $property
	 * @param [type] $value
	 * @return void
	 * @dataProvider wrongDataType
	 */
	public function testValidateWithWrongDataType($property, $value){
		$user = clone $this->user;
		// echo "Propiedad: {$property}, Valor: ";
		$user->$property = $value;
		$validated = $user->validate();
		$this->assertFalse($validated);
	}

	public static function wrongDataType(){
		return [
			['username', 25],
			['username', true],
			['username', false],
			['email', 25],
			['email', 'pepe@pepeillocom'],
			['email', 'pepepepeillo.com'],
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $value
	 * @return void
	 * @dataProvider isNumberData
	 */
	public function testIsNumber($value){
		$isNumber =  UserAuthModelPrivateToPublic::isNumber($value);
		$this->assertTrue($isNumber);
	}

	public static function isNumberData(){
		return [
			[25],
			[true],
			['025'],
			['-251'],
			['568.0987'],
		];
	}

	public function testIsEmail(){
		$isEmail = UserAuthModelPrivateToPublic::isEmail('pepe@pepeillo.com');
		$this->assertTrue($isEmail);
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $value
	 * @return void
	 * @dataProvider isEmailData
	 */
	public function testIsNotEmail($value){
		$isEmail = UserAuthModelPrivateToPublic::isEmail($value);
		$this->assertFalse($isEmail);
	}

	public static function isEmailData(){
		return [
			[25],
			[true],
			['pepe@pepeillocom'],
			['pepepepeillo.com'],
			['568.0987'],
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @param [type] $params
	 * @param [type] $return
	 * @return void
	 * @dataProvider getData
	 */
	public function testGet($query, $values, $return){
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('selectAssoc', $query, $values, $return);
		UserAuthModel::$db = $db;
		// Obtenemos el objeto usuario desde UserAuthModel
		$user = UserAuthModel::get($values[':propertyUser']);
		// Comprobaciones de clase
		
		$this->assertSame(UserAuthModel::class, $user::class);
		$this->assertSame($db::class, $user::$db::class);
		// Comprobación de datos
		$userData = $return[0];
		$this->assertSame($userData['id'], $user->id);
		$this->assertSame($userData['username'], $user->username);
		$this->assertSame($userData['email'], $user->email);
		$this->assertSame($userData['password'], $user->password);
	}

	public static function getData(){
		$query = "SELECT * FROM user WHERE id = :propertyUser OR username = :propertyUser OR email = :propertyUser";
		return [
			[$query, [':propertyUser' => 1], self::getDataUser('josemi')],
			[$query, [':propertyUser' => 'josemi'], self::getDataUser('josemi')],
			[$query, [':propertyUser' => 'josemi@josemi.com'], self::getDataUser('josemi')],
			[$query, [':propertyUser' => 2], self::getDataUser('pacorro')],
			[$query, [':propertyUser' => 'pacorro'], self::getDataUser('pacorro')],
			[$query, [':propertyUser' => 'pacorro@pacorro.com'], self::getDataUser('pacorro')],
		];
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @param [type] $params
	 * @param [type] $return
	 * @return void
	 * @dataProvider getDataWrongParams
	 */
	public function testGetWrongParams($query, $values, $return){
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('selectAssoc', $query, $values, $return);
		UserAuthModel::$db = $db;
		// Obtenemos el objeto usuario desde UserAuthModel
		$userModel = new UserAuthModel(null, $db);
		$user = UserAuthModel::get($values[':propertyUser']);
		// Comprobaciones de clase
		
		$this->assertSame(UserAuthModel::class, $user::class);
		$this->assertSame($db::class, $user::$db::class);
		// Comprobación de datos. Deben estar vacíos
		$this->assertSame(null, $user->id);
		$this->assertSame('', $user->username);
		$this->assertSame('', $user->email);
		$this->assertSame('', $user->password);
	}

	public static function getDataWrongParams(){
		$query = "SELECT * FROM user WHERE id = :propertyUser OR username = :propertyUser OR email = :propertyUser";
		return [
			[$query, [':propertyUser' => 3], []],
			[$query, [':propertyUser' => 'joseMiguel'], []],
			[$query, [':propertyUser' => 'josemijosemi.com'], []],
			[$query, [':propertyUser' => 5], []],
			[$query, [':propertyUser' => 'pacorroPaque'], []],
			[$query, [':propertyUser' => 'pacorro128@pacorro.com'], []],
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @param [type] $params
	 * @param [type] $return
	 * @return void
	 * @dataProvider getUsersData
	 */
	public function testGetUsers($query, $return) {
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('selectAssoc', $query, null, $return);
		UserAuthModel::$db = $db;
		// Obtenemos el objeto usuario desde UserAuthModel
		$users = UserAuthModel::getUsers();
		$this->assertIsArray($users);
		$this->assertSame(count(self::$dataUsers), count($users));
		foreach ($users as $user) {
			$this->assertSame(UserAuthModel::class, $user::class);
			$this->assertSame($db::class, $user::$db::class);
			// Comprobación de datos
			$userData = self::$dataUsers[$user->username];
			$this->assertSame($userData['id'], $user->id);
			$this->assertSame($userData['username'], $user->username);
			$this->assertSame($userData['email'], $user->email);
			$this->assertSame($userData['password'], $user->password);
		}
		
	}

	public static function getUsersData(){
		$query = "SELECT * FROM user";
		return [
			[$query, self::getDataUsers()]
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @param [type] $params
	 * @param [type] $return
	 * @return void
	 * @dataProvider getUsersDataWithLimitAndOffset
	 */
	public function testGetUsersWithLimitAndOffset($limit, $offset, $count, $return) {
		$query = "SELECT * FROM user LIMIT {$limit} OFFSET {$offset}";
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('selectAssoc', $query, null, $return);
		UserAuthModel::$db = $db;
		// Obtenemos varios usuarios
		$users = UserAuthModel::getUsers($limit, $offset);
		$this->assertIsArray($users);
		$this->assertSame($count, count($users));
		foreach ($users as $user) {
			$this->assertSame(UserAuthModel::class, $user::class);
			$this->assertSame($db::class, $user::$db::class);
			// Comprobación de datos
			$userData = self::$dataUsers[$user->username];
			$this->assertSame($userData['id'], $user->id);
			$this->assertSame($userData['username'], $user->username);
			$this->assertSame($userData['email'], $user->email);
			$this->assertSame($userData['password'], $user->password);
		}
		
	}

	public static function getUsersDataWithLimitAndOffset(){
		
		return [
			[0, 0, 0, []], // Límite 0 devuelve 0 usuarios
			[1, 1, 1, self::getDataUser('pacorro')],
			[1, 0, 1, self::getDataUser('josemi')],
			[1, 1, 1, self::getDataUser('pacorro')],
		];
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $query
	 * @param [type] $params
	 * @param [type] $return
	 * @return void
	 * @dataProvider getUsersDataOutOfLimits
	 */
	public function testGetUsersOutOfLimits($limit, $offset, $count, $return) {
		$query = "SELECT * FROM user LIMIT {$limit} OFFSET {$offset}";
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('selectAssoc', $query, null, $return);
		UserAuthModel::$db = $db;
		// Creamos el objeto UserAuthModel y obtenemos un usuario
		$users = UserAuthModel::getUsers($limit, $offset);
		$this->assertIsArray($users);
		$this->assertSame($count, count($users));
		foreach ($users as $user) {
			$this->assertSame(UserAuthModel::class, $user::class);
			$this->assertSame($db::class, $user::$db::class);
			// Comprobación de datos
			$userData = self::$dataUsers[$user->username];
			$this->assertSame($userData['id'], $user->id);
			$this->assertSame($userData['username'], $user->username);
			$this->assertSame($userData['email'], $user->email);
			$this->assertSame($userData['password'], $user->password);
		}
		
	}

	public static function getUsersDataOutOfLimits(){
		
		return [
			[0, 0, 0, []], // Límite cero no devuelve nada
			[0, 1, 0, []],
			[20, 2, 0, []], // Offset fuera de rango
		];
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 * @dataProvider updateData
	 */
	public function testUpdate($dataUser, $values, $return){
		$query = "UPDATE user SET username=:username, email=:email, password=:password WHERE id=:id LIMIT 1";
		// Cambiamos los valores a actualizar
		$values[':email'] = 's' . $values[':email'];
		unset($values[':passwordToHash']);
		$dataUser['email'] = 's' . $dataUser['email'];
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('insertUpdateQuery', $query, $values, $return);
		UserAuthModel::$db = $db;
		$user = new UserAuthModelPrivateToPublic($dataUser);
		$this->assertSame($return, $user->update());
	}

	public static function updateData(){
		return [
			[self::getCloneUserData('josemi'), self::getDataForInsertUpdate('josemi'), true],
			[self::getCloneUserData('pacorro'), self::getDataForInsertUpdate('pacorro'), true]

		];
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 * @dataProvider insertDataExistingUsers
	 */
	public function testInsertExistingUsers($dataUser, $values, $return){
		$query = "INSERT INTO user (username, email, password) VALUES (:username, :email, :password)";
		unset($values[':passwordToHash']);
		unset($values[':id']);
		unset($dataUser['passwordToHash']);
		unset($dataUser['id']);
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('insertUpdateQuery', $query, $values, $return);
		UserAuthModel::$db = $db;
		$user = new UserAuthModelPrivateToPublic($dataUser);
		$this->assertSame($return, $user->insert());

	}

	public static function insertDataExistingUsers(){
		return [
			// Si intentamos introducir usuarios que ya existen, debe fallar
			[self::getCloneUserData('josemi'), self::getDataForInsertUpdate('josemi'), false],
			[self::getCloneUserData('pacorro'), self::getDataForInsertUpdate('pacorro'), false]
		];
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 * @dataProvider insertData
	 */
	public function testInsert($dataUser, $values, $return){
		$query = "INSERT INTO user (username, email, password) VALUES (:username, :email, :password)";
		unset($values[':passwordToHash']);
		unset($values[':id']);
		unset($dataUser['passwordToHash']);
		unset($dataUser['id']);
		// Creamos el mock de la base de datos
		$db = $this->createMockForMethod('insertUpdateQuery', $query, $values, $return);
		UserAuthModel::$db = $db;
		$user = new UserAuthModelPrivateToPublic($dataUser);
		$this->assertSame($return, $user->insert());

	}

	public static function insertData(){
		return [
			[
				[
					'username' => 'josemi2',
					'email' => 'josemi2@josemi.com',
					'password' => '$2y$10$ISWn21sOxa8Z/qovOFo3L.nvk8CkgYyo7UYhrUr/779vlqNvG2INK'
				], 
				[
					':username' => 'josemi2',
					':email' => 'josemi2@josemi.com',
					':password' => '$2y$10$ISWn21sOxa8Z/qovOFo3L.nvk8CkgYyo7UYhrUr/779vlqNvG2INK'
				], 
				true
			],
			[
				[
					'username' => 'pacorro2',
					'email' => 'pacorro2@pacorro.com',
					'password' => '$2y$10$iPe4shZQS5cac2uZQiFV9e4QN3pvznkL3u88r4Q1u9PW7HL90fRPa'
				], 
				[
					':username' => 'pacorro2',
					':email' => 'pacorro2@pacorro.com',
					':password' => '$2y$10$iPe4shZQS5cac2uZQiFV9e4QN3pvznkL3u88r4Q1u9PW7HL90fRPa'
				], 
				true
			],
		];
	}
}