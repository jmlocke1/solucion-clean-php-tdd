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
	public static function register(string $username, string $email, string $password): bool {
		$user = new UserAuthModel();
		$registered = $user->register($username, $email, $password);
		$saved = false;
		if($registered){
			$saved = $user->save();
		}
		if($registered && $saved){
			echo "Usuario registrado con éxito";
		}else{
			echo "Ha ocurrido un error. Usuario no registrado";
		}
		return $registered && $saved;
	}

	public static function login(string $email, string $password): bool {
		return false;
	}
}