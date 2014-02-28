<?php

return array
(
	'default' => array
	(
		'type'       => 'MySQL',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 * array    variables    system variables as "key => value" pairs
			 *
			 * Ports and sockets may be appended to the hostname.
			 */

			'hostname'   => (LOCAL)? '127.0.0.1' : 'ap-cdbr-azure-east-b.cloudapp.net',
			'username'   => (LOCAL)? 'edifice2_ibank':'b987d2f889b1da',
			'password'   => (LOCAL)? 'ide@bank2014':'50fe9ed6',
			'database'   => (LOCAL)? 'edifice2_ideabank':'ideabankdemo',

			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
	)
);
