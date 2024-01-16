<?php
namespace Model;
require_once __DIR__."/../config/app.php";


use Model\database\DB;

// Conexión DB

// Crear una clase que internamente maneje los querys

class UserAuthModel {
	const TABLENAME = 'user';
	public static $db;
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
		if(!is_null($db)){
			self::$db = $db;
		}elseif(!isset(self::$db)){
			self::$db = DB::getDB();
		}
		
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
		if($validated){
			$this->password = self::hashPassword($password);
		}
		return false;
	}

	public static function hashPassword(string $password): string {
		return password_hash($password, PASSWORD_DEFAULT);
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
		$validate = $validate && !empty(trim($this->username)) && is_string($this->username) && !$this->isNumber($this->username);
		$validate = $validate && !empty(trim($this->email)) && $this->isEmail($this->email);
		$validate = $validate && !empty(trim($this->password)) && is_string($this->password);
		return $validate;
	}

	protected static function isNumber($value): bool{
		$val = preg_match('/^-?\d+(\.\d+)?$/', $value);
		return $val === 1;
	}

	protected static function isEmail($value): bool{
		$val =filter_var($value, FILTER_VALIDATE_EMAIL);
		if(is_bool($val)) return $val;
		else return is_string($val);
	}

	
	public static function get($propertyUser): static{
		self::hayDB();
		$query = "SELECT * FROM " . self::TABLENAME . " WHERE ";
		$query .= "id = :propertyUser OR ";
		$query .= "username = :propertyUser OR ";
		$query .= "email = :propertyUser";
		$values = [':propertyUser' => $propertyUser];
		try {
			$result=self::$db->selectAssoc($query, $values);
		} catch (\Throwable $th) {
			$result = [];
		}
		
		$data = array_shift($result);
		return new static($data);
	}

	/**
	 * Devuelve un array con los datos de todos los usuarios, con los
	 * límites establecidos por parámetro
	 *
	 * @param integer $limit
	 * @param integer $offset
	 * @return void
	 */
	public static function getUsers(int $limit = -1, int $offset = 0): array{
		self::hayDB();
		$query = "SELECT * FROM " . self::TABLENAME;
		if($limit >= 0){
			$query .= " LIMIT {$limit} OFFSET {$offset}";
		}
		try {
			$result=self::$db->selectAssoc($query);
		} catch (\Throwable $th) {
			$result = [];
		}
		
		return self::getObjects($result);
	}


	protected static function getObjects(array $data): array {
		$objects = [];
		foreach($data as $dataObject){
			$objects[] = new static($dataObject, self::$db);
		}
		return $objects;
	}

	protected static function hayDB(){
		if(!isset(self::$db)) self::$db = DB::getDB();
	}

	public function save(){
		if(isset($this->id)){
			return $this->update();
		}else{
			return $this->insert();
		}
	}

	protected function update(): bool {
		$query = "UPDATE ". self::TABLENAME . " SET username=:username, email=:email, password=:password WHERE id=:id LIMIT 1";
		$values = [
			':id' => $this->id,
			':username' => $this->username,
			':email' => $this->email,
			':password' => $this->password
		];

		return self::$db->insertUpdateQuery($query, $values);
	}

	protected function insert(): bool {
		$query = "INSERT INTO ". self::TABLENAME ." (username, email, password) VALUES (:username, :email, :password)";
		$values = [
			':username' => $this->username,
			':email' => $this->email,
			':password' => $this->password
		];
		return self::$db->insertUpdateQuery($query, $values);
	}
}