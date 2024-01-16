<?php
namespace Model\database;
require_once __DIR__."/../../config/app.php";
use PDO;
use Util\Klog;
use App\config\Config;

class DB {
	protected $conexion;
    protected static $msg;
    protected static $affectedRows;

    // Gestor de conexiones
    protected static $dbManager = [];

	public function __construct(
        $host = Config::DB_HOST,
        $dbName = Config::DB_NAME,
        $dbEncode = Config::DB_ENCODE,
        $dbUsername = Config::DB_USERNAME,
        $dbPassword = Config::DB_PASSWORD
    ){
		$this->conexion = self::conectar($host, $dbName, $dbEncode, $dbUsername, $dbPassword);
	}

    public static function getDB(
        $host = Config::DB_HOST,
        $dbName = Config::DB_NAME,
        $dbEncode = Config::DB_ENCODE,
        $dbUsername = Config::DB_USERNAME,
        $dbPassword = Config::DB_PASSWORD
    ){
        if(isset(self::$dbManager[$host][$dbName])){
            return self::$dbManager[$host][$dbName];
        }else{
            self::$dbManager[$host][$dbName] = new static($host, $dbName, $dbEncode, $dbUsername, $dbPassword);
            return self::$dbManager[$host][$dbName];
        }
    }

	/**
     * Conecta con la base de datos con los datos que hay en Config.php
     */
    protected static function conectar($host, $dbName, $dbEncode, $dbUsername, $dbPassword): PDO{
        $dsn = "mysql:host=".$host.";dbname=".$dbName.";charset=".$dbEncode;
        try {
            $conexion = new PDO($dsn, $dbUsername, $dbPassword);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $exc) {
            self::$msg = "Error: ".$exc->getMessage() . " Conectando a la base de datos<br>";
            self::$msg .= $exc->getTraceAsString();
            Klog::error(self::$msg);
        }
        return $conexion;
    }

	/**
     * Ejecuta una consulta preparada
     * 
     * @param string $sql       Consulta parametrizada (o no)
     * @param array $valores    Valores para insertar/actualizar. Puede ser un array de arrays de valores
     * @param boolean $multiInsert  Flag que indica si los valores son un array de array de valores
     * @return type
     */
    public function ejecutarConsulta(string $sql, array $valores = null, bool $multiInsert = false) {
        self::$msg = null;
        $exito = true;
        self::$affectedRows = 0;
        try {
            $stmt = $this->conexion->prepare($sql);
            if($multiInsert){
                foreach ($valores as $value) {
                    $exito = $exito && $stmt->execute($value);
                    self::$affectedRows += $stmt->rowCount();
                }
            }else{
                $exito = $stmt->execute($valores);
                self::$affectedRows += $stmt->rowCount();
            }
        } catch (\PDOException $exc) {
            self::$msg = "Error ".$exc->getMessage();
            $exito = false;
        }
        if(!$exito){
            self::$msg .= ". Error en la inserción";
            $errmsg = "Consulta efectuada: ".PHP_EOL;
            $errmsg .= $sql.PHP_EOL;
            $errmsg .= "Valores a insertar: ".json_encode($valores).PHP_EOL;
            $errmsg .= self::$msg;
            Klog::error($errmsg);
        }
        return $stmt;
    }

    /**
     * Función para insertar o actualizar un registro
     *
     * @param string $sql
     * @param array|null $values
     * @param boolean $multiInsert
     * @return boolean  true si ha habido éxito en la inserción/actualización
     */
    public function insertUpdateQuery(string $sql, array $values = null, bool $multiInsert = false): bool {
        $this->ejecutarConsulta($sql, $values, $multiInsert);
        return !isset(self::$msg) && self::$affectedRows > 0;
    }

	/**
     * Ejecuta una consulta select y retorna un array asociativo en cada fila
     * 
     * @param string $sql
     * @param array $valores
     * @throws Exception
     */
    public function selectAssoc($sql, $valores = null){
        $resp = $this->ejecutarConsulta($sql, $valores);
        $resultado = [];
        while($fila = $resp->fetch(PDO::FETCH_ASSOC)){
            $resultado[] = $fila;
        }
        self::hayError();
        return $resultado;
    }

    /**
     * Undocumented function
     *
     * @param [type] $sql
     * @param [type] $valores
     * @return array
     * @throws Exception
     */
	public function selectObject($sql, $valores = null): array {
        $resp = $this->ejecutarConsulta($sql, $valores);
        $resultado = [];
        while($fila = $resp->fetch(PDO::FETCH_OBJ)){
            $resultado[] = $fila;
        }
        self::hayError();
        return $resultado;
    }

	/**
     * Comprueba si se ha provocado algún error
     * @throws Exception
     */
    public static function hayError() {
        if(!empty(self::$msg)){
            throw new \Exception(self::$msg);
        }
    }

    public static function getMsg(){
        return self::$msg;
    }
}