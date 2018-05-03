<?php
/**
 * Clace de encriptación para JWT
 * @var String
 */
$config['jwt_key'] = 'S0p3r_S3c3t_k3y';

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
