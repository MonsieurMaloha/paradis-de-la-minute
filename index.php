<?php

	define('DIR_APP' , __DIR__.'/app/');
	define('DIR_BOOTSTRAP' , './bootstrap/');

	require DIR_BOOTSTRAP.'autoload.php';
	require DIR_BOOTSTRAP.'app.php';

 
 	//dd( message::getAchievment('100') );


	// Contrôle du token
	if (slack::getToken() != $_POST['token']){
		exit(message::getMessage('unauthorized'));
	}

	// Contrôle sur le user-agent
	if ( $_SERVER['HTTP_USER_AGENT'] != 'Slackbot 1.0 (+https://api.slack.com/robots)'){
		exit(message::getMessage('useragent'));
	}

	// Récupération des paramètres envoyer par slack
	$get_params = controller::getParameters($_POST['text']);

	// Log des actions et des ips
	$ip = ip::getIp($_SERVER);
    log::add($_POST['user_id'] , $get_params[0] , $ip);

    // Si IP bloquée, on stoppe le traitement
 	if ( antitriche::isCheater($ip) ) {
		exit(message::getMessage('cheater'));
	}

	// Traitement et affichage du retour de la demande
    header('Content-Type: application/json');

    $controller = new controller($_POST);
	echo $controller->render();