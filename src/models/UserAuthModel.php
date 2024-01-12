<?php
namespace Model;

use Model\database\DB;

// Conexión DB

// Crear una clase que internamente maneje los querys

class UserAuthModel {
	const TABLENAME = 'user';
	public $db;
	/**
	 * Hago notar que exponer los campos públicamente no es una buena
	 * práctica en absoluto, no obstante, dado que es un ejercicio,
	 * no veo necesidad de desarrollar una arquitecura más compleja.
	 */
	public ?int $id;
	public string $username;
	public string $email;
	public string $password;


	public function __construct($args = null, $db = null)
	{
		$this->db = $db ?? new DB();
		
		$this->id = $args['id'] ?? null;
		$this->username = $args['username'] ?? '';
		$this->email = $args['email'] ?? '';
		$this->password = $args['password'] ?? '';

	}
	// register(){} Correos únicos
	// login(){}
	public function register(string $username, string $email, string $password): bool {
		$this->username = $username;
		$this->email = $email;
		$this->password = $password;
		$validated = $this->validate();
		return false;
	}

	/**
	 * Función de validación del modelo.
	 * Es una función muy sencilla, no genera mensajes de
	 * error personalizados, dado que el requisito es que
	 * dé un error genérico en caso de fallo
	 *
	 * @return boolean
	 */
	public function validate(): bool{
		$validate = true;
		if(!is_null($this->id)){
			$validate = $validate && is_int($this->id) && $this->id > 0;
		}
		$validate = $validate && !empty($this->username) && is_string($this->username);
		$validate = $validate && !empty($this->email) && is_string($this->email);
		$validate = $validate && !empty($this->password) && is_string($this->password);
		return $validate;
	}

	public function getUsers($limit = 50, $offset = 0){
		$query = "SELECT * FROM " . self::TABLENAME . " LIMIT :limit OFFSET :offset";
		$values = [
			':limit' => $limit,
			':offset' => $offset
		];
		$result=$this->db->selectAssoc($query, $values);
		return $result;
	}
}