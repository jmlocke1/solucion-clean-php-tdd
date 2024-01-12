<?php

use Model\UserAuthModel;
use PHPUnit\Framework\TestCase;

class UserAuthModelTest extends TestCase {
	public function testRegister() {
		$user = new UserAuthModel();
		$registrado = $user->register('Pepe', 'pepe@pepeillo.com', 'yomismo');
		$this->assertTrue($registrado);
	}

	public function testValidate() {
		$user = new UserAuthModel();
		$validated = $user->validate();
		$this->assertTrue($validated);
	}
}