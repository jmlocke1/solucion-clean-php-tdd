<?php
namespace Controller;
// Crear una clase de Autenticación

// Register y Login retornan True o False
class UserAuth {
	public $db;
	public function __construct($db = null)
	{
		$this->db = $db;
	}
	public function register(string $username, string $email, string $password): bool {
		return false;
	}
}