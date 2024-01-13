<?php
namespace Model\database;
require_once __DIR__."/../../config/app.php";
use PDO;
use Util\Klog;
use App\config\Config;

class DB {
	public static $conexion;
    protected static $msg;
    protected static $affectedRows;

	public function __construct($conexion = null)
	{
		if(is_null($conexion)){
			self::conectar();
		}
	}

	/**
     * Conecta con la base de datos con los datos que hay en Config.php
     */
    protected static function conectar(){
        $dsn = "mysql:host=".Config::DB_HOST.";dbname=".Config::DB_NAME.";charset=".Config::DB_ENCODE;
        try {
            self::$conexion = new PDO($dsn, Config::DB_USERNAME, Config::DB_PASSWORD);
            self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $exc) {
            self::$msg = "Error: ".$exc->getMessage() . " Conectando a la base de datos<br>";
            self::$msg .= $exc->getTraceAsString();
            Klog::error(self::$msg);
        }
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
            $stmt = self::$conexion->prepare($sql);
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

	public function selectObject($sql, $valores = null) {
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
        if(isset(self::$msg)){
            throw new \Exception(self::$msg);
        }
    }
}