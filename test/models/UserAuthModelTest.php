<?php

use Model\UserAuthModel;
use PHPUnit\Framework\TestCase;

class UserAuthModelPrivateToPublic extends UserAuthModel {
	public static function isNumber($value) : bool {
		return parent::isNumber($value);
	}

	public static function isEmail($value) : bool {
		return parent::isEmail($value);
	}
}
class UserAuthModelTest extends TestCase {
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
		$user = new UserAuthModelPrivateToPublic();
		$isNumber = $user->isNumber($value);
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
		$user = new UserAuthModelPrivateToPublic();
		$isEmail = $user->isEmail('pepe@pepeillo.com');
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
		$user = new UserAuthModelPrivateToPublic();
		$isEmail = $user->isEmail($value);
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

}