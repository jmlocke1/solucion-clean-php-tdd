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

	public function testLogin(){
		$this->assertTrue(UserAuth::login('josemi@josemi.com', 'Josemi123'));
	}
}