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
			// Cette IP (via REMOTE_ADDR) est plus sûr, contrairement aux 2 autres (HTTP_CLIENT_IP et HTTP_X_FORWARDED_FOR) 
			// qui peuvent être injecté via les entetes HTTP et ne sont donc pas fiable du tout ! (A éviter).
			if (!empty( $server['REMOTE_ADDR'])) {
			    $ip = $server['REMOTE_ADDR'];
			} elseif (!empty($server['HTTP_CLIENT_IP'])) {
			    $ip = $server['HTTP_CLIENT_IP'];
			} elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
			    $ip = $server['HTTP_X_FORWARDED_FOR'];
			} 
			return ip;
		}


	}
