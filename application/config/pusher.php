<?php
/*
|--------------------------------------------------------------------------
| Credenciales
|--------------------------------------------------------------------------
| Credenciales obigatorias para identificar el socket de conecxión.
|
| pusher_app_id      string  ID de la aplicación de Pusher.
| pusher_app_key     string  Clave de la aplicación.
| pusher_app_secret  string  Token de la aplicación.
| pusher_debug       bool    Encender/Apagar mensajes de debugeo, se encian a los logs de CI.
|
*/
$config['pusher_app_id']     = '507090';
$config['pusher_app_key']    = '11317f04cabab4346f97';
$config['pusher_app_secret'] = '4f28223c4d7b99f29818';
$config['pusher_cluster']    = 'us2';
$config['pusher_debug']      = FALSE;

/*
|--------------------------------------------------------------------------
| Other parameters
|--------------------------------------------------------------------------
|
| Optional parameters that can be configures. Uncomment the parameters
| that you want to use.
|
| pusher_scheme     string  e.g. http or https.
| pusher_host       string  The host e.g. api.pusherapp.com. No trailing forward slash.
| pusher_port       int     The http port.
| pusher_timeout    int     The HTTP timeout.
| pusher_encrypted  bool    Quick option to use scheme of https and port 443.
|
*/
// $config['pusher_scheme']    = 'http';
// $config['pusher_host']      = '';
// $config['pusher_port']      = 80;
// $config['pusher_timeout']   = 30;
$config['pusher_encrypted'] = TRUE;
