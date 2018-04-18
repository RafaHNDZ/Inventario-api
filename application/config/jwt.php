<?php
/**
 * Clace de encriptación para JWT
 * @var String
 */
$config['jwt_key'] = '';

/**
 * Algoritmos de encriptación
 * @var Array
 */
$config['jwt_algo'] = ['HS256'];

/**
 * Tiempo de vida del token
 * @var Int
 */
$config['jwt_exp'] = (60 * 60);
