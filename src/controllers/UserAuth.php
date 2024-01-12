<?php
namespace Controller;

use Model\UserAuthModel;

// Crear una clase de Autenticación

// Register y Login retornan True o False
class UserAuth {
	public $db;
	public function __construct($db = null)
	{
		/**
		 * Esta solución está muy cogida por los pelos, pero es lo que se me ha ocurrido para
		 * poder pasar fácilmente un mock de la base de datos.
		 * Cualquier sugerencia para mejorar esto (que es claramente mejorable), será bienvenida
		 */
		$this->db = $db;
	}
	public function register(string $username, string $email, string $password): bool {
		$user = new UserAuthModel(null, $this->db);
		$registered = $user->register($username, $email, $password);
		return $registered;
	}
}