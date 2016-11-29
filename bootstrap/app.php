<?php

// Chargement du fichier de settings
$GLOBALS['ini_settings'] = $ini_settings = parse_ini_file(__DIR__."/../settings.ini");

// Chargement des fonctions
require DIR_APP.'inc/function.php';

// Connection à la base et instanciation de PDO
require DIR_APP.'inc/database.php';

