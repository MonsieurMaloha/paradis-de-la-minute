<?php
/*
 * Gestion des logs (super basique).
 */ 

	class log {


		/**
		 * Ajoute un log dans la base.
		 * On sauvegarde l'utilisateur, l'action demandée et l'IP.
		 * @param string $user
		 * @param string $action
		 * @param string $ip
		 */
		public static function add($user , $action , $ip) {
			
			$pdo = $GLOBALS['pdo'];

			$sql = "INSERT INTO log (user_id, action, created_at , ip) VALUES (:user_id, :action , :created_at , :ip)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':user_id', $user);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':created_at', date('Y:m:d H:i:s') );
            $stmt->bindParam(':ip', $ip);

            $stmt->execute();
		}


	}