<?php
namespace Controller;

use Model\UserAuthModel;

// Crear una clase de Autenticación

// Register y Login retornan True o False
class UserAuth {
	
	
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
		$user = new UserAuthModel();
		$logged = $user->login($email, $password);
		if($logged){
			echo "Ha iniciado sesión correctamente";
		}else{
			echo "Ha ocurrido un error. No se ha iniciado la sesión";
		}
		return $logged;
	}
}