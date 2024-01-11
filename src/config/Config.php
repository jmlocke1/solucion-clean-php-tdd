<?php
namespace App\config;

class Config {
	/**
     * Servidor de la base de datos
     */
    const DB_HOST = ConfigLocal::DB_HOST;
    /**
     * Nombre de la base de datos
     */
    const DB_NAME = ConfigLocal::DB_NAME;
    /**
     * Usuario de la base de datos
     */
    const DB_USERNAME = ConfigLocal::DB_USERNAME;
    /**
     * Contraseña del usuario de la base de datos
     */
    const DB_PASSWORD = ConfigLocal::DB_PASSWORD;
}