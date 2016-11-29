<?php
	$dns = 'mysql:host='.$ini_settings['host'].';dbname='.$ini_settings['base'];

	$GLOBALS['pdo'] = $pdo = new PDO($dns, $ini_settings['user'], $ini_settings['pass']);