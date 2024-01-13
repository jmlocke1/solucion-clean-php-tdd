<?php
namespace Test\controllers;
require_once __DIR__."/../../src/config/app.php";

use Controller\UserAuth;
use Model\UserAuthModel;
use PHPUnit\Framework\TestCase;

/**
 * Creen una clase REAL de Autenticación
 * 
 * 1. Registrar Usuarios - username, password, email.
 * 2. Hash Password - Blowfish / Bcrypt (password_hash)
 * 3. Iniciar Sesión - email, password
 */

class UserAuthTest extends TestCase {
	public function testRegister(){
		$user = new UserAuthModel();
		
		$registrado = $user->register('Pepe', 'pepe@pepeillo.com', 'yomismo');
		$this->assertTrue($registrado);
	}
}