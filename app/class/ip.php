<?php
/*
 * Gère les IPs.
 */ 

	class ip {

		/**
		 * Retourne l'IP de l'utilisateur.
		 * @param array $server
		 * @return string
		 */
		public static function getIp($server) {
			if (!empty($server['HTTP_CLIENT_IP'])) {
			    $ip = $server['HTTP_CLIENT_IP'];
			} elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $server['HTTP_X_FORWARDED_FOR'];
			} else {
			    $ip = $server['REMOTE_ADDR'];
			}
			return ip;
		}


	}