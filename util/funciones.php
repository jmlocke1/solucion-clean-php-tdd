<?php

/**
 * Función simple que ayuda a depurar programas. No sustituye a un debug, pero puede
 * ser mucho más rápido. Imprime la variable a comprobar y corta la ejecución
 *
 * @param [type] $variable
 * @return void
 */
function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

/**
 * Función simple que ayuda a depurar programas. No sustituye a un debug, pero puede
 * ser mucho más rápido. Imprime la variable a comprobar pero no corta la ejecución
 *
 * @param [type] $variable
 * @return void
 */
function debuguearSinExit($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
}