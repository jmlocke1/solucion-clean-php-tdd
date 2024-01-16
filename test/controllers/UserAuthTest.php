<?php
namespace Test\controllers;
require_once __DIR__."/../../src/config/app.php";

use Controller\UserAuth;

use Model\UserAuthModel;
use Test\models\database\DBMock;
use Test\models\UserAuthModelTest;

/**
 * Creen una clase REAL de Autenticación
 * 
 * 1. Registrar Usuarios - username, password, email.
 * 2. Hash Password - Blowfish / Bcrypt (password_hash)
 * 3. Iniciar Sesión - email, password
 */

class UserAuthTest extends DBMock {

	/**
	 * Undocumented function
	 *
	 * @return void
	 * @dataProvider Test\models\UserAuthModelTest::registerData
	 */
	public function testRegister($values, $returnRegister, $returnSave){
		$db = $this->createMockForMethodWithoutEntries('insertUpdateQuery', $returnSave);
		UserAuthModel::$db = $db;
		$registrado = UserAuth::register($values[':username'], $values[':email'], $values[':password']);
		$this->assertSame($returnRegister && $returnSave, $registrado);
		$message = $returnRegister && $returnSave ?
			"Usuario registrado con éxito" :
			"Ha ocurrido un error. Usuario no registrado";
		$this->expectOutputString($message);
	}
	/**
	 * Undocumented function
	 *
	 * @return void
	 * @dataProvider Test\models\UserAuthModelTest::loginData
	 */
	public function testLogin($email, $password, $dataUser, $result){
		$queryGet = "SELECT * FROM user WHERE id = :propertyUser OR username = :propertyUser OR email = :propertyUser";
		$valGet = [':propertyUser' => $email];
		
		
		$db = $this->createMockForMethod('selectAssoc', $queryGet, $valGet, $dataUser);
		$db = $this->createMockForMethodWithoutEntries('insertUpdateQuery', $result, $db);
		
		UserAuthModel::$db = $db;
		$logged = UserAuth::login($email, $password);
		$this->assertSame($result, $logged);
		$message = $logged ?
			"Ha iniciado sesión correctamente" :
			"Ha ocurrido un error. No se ha iniciado la sesión";
		$this->expectOutputString($message);
	}
}