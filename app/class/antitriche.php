<?php
/*
 * Contrôle basique sur les tentatives de triches.
 */ 

	class antitriche {

		/**
		 * @var array IP des tricheurs.
		 */
		public static $ips_cheater = ['212.227.116.145'];


		/**
		 * Test si l'utilisateur courant est considéré comme un tricheur.
		 * @param string $ip
		 * @return boolean
		 */
		public static function isCheater($ip) {

			if (self::checkIp( $ip )) {
				return true;
			}

			return false;

		}


		/**
		 * Contrôle si l'IP est répertoriée dans l'IP des tricheurs.
		 * @param string $ip
		 * @return boolean
		 */
		public static function checkIp( $ip ) {
			return in_array($ip , self::$ips_cheater );
		}

	}