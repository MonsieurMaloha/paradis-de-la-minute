<?php
/*
 * Gestion de la collection des minutes.
 */ 

	class pdm {


		/**
		 * Essaie d'ajouter une minute à la collection.
		 * @param string $minute
		 * @param string $user
		 * @return boolean
		 */
		public static function add($minute,  $user) {

            $pdo = $GLOBALS['pdo'];

			if (self::exist($minute,  $user) == 0) {

				$sql = "INSERT INTO pdm_minutes (user_id, minute, created_at) VALUES (:user_id, :minute, :created_at)";
                $stmt = $pdo->prepare($sql);

                $stmt->bindParam(':user_id', $user);
                $stmt->bindParam(':minute', $minute);
                $stmt->bindParam(':created_at', date('Y:m:d H:i:s'));

                $stmt->execute();

				return true;
			} else {
				return false;
			}

		}


		/**
		 * Contrôle si une minute existe déjà dans la collection.
		 * @param string $minute
		 * @param string $user
		 * @return int
		 */
		public static function exist($minute,  $user) {

    		$pdo = $GLOBALS['pdo'];

			$sql = 'SELECT id FROM pdm_minutes WHERE user_id = :user_id AND minute = :minute';
            $sth = $pdo->prepare($sql);
            $sth->execute([':user_id' => $user , ':minute' => $minute]);

   			return $sth->rowCount();
		}


		/**
		 * Compte le nombre de minute dans la collection d'un utilisateur.
		 * @param string $user
		 * @return int
		 */
		public static function count($user) {

			$pdo = $GLOBALS['pdo'];

			$sql = 'SELECT id FROM pdm_minutes WHERE user_id = :user_id';
            $sth = $pdo->prepare($sql);
            $sth->execute([':user_id' => $user]);

			return $sth->rowCount();
		}


		/**
		 * Compte le nombre total de minutes collectées dans la partie.
		 * @return int
		 */
		public static function total() {

			$pdo = $GLOBALS['pdo'];

			$sql = 'SELECT id FROM pdm_minutes';
            $sth = $pdo->prepare($sql);
            $sth->execute();

			return $sth->rowCount();

		}


		/**
		 * Retourne toutes les minutes possédées et non possédées dans une heure pour un utilisateur.
		 * @param string $user_id
		 * @param string $heure
		 * @return array
		 */
		public static function hour($user_id , $heure) {

            $pdo = $GLOBALS['pdo'];

   			$have = [];
			$all = [];

			for ($i = 0; $i <= 59; $i++) {
				$minute = str_pad($i, 2, "0", STR_PAD_LEFT);
                $all[$heure.$minute] = $heure.$minute;
			}

			$sql = 'SELECT minute FROM pdm_minutes WHERE user_id = '.$user_id.' AND minute LIKE "'.$heure.'%" ORDER BY minute';
		    foreach  ($pdo->query($sql) as $row) {
			
                if (isset( $all[$row['minute']] ) )
		            $all[$row['minute']] = "~".$all[$row['minute']]."~";
		  	}

			return $all;

		}



		/**
		 * Retourne le classement des meilleurs joueurs.
		 * @param int $many
		 * @return array
		 */
		public static function top($many = 5) {

			$pdo = $GLOBALS['pdo'];

			$top = [];

			$sql = 'SELECT COUNT(id) AS count_id , user_id FROM pdm_minutes GROUP BY user_id ORDER BY count_id DESC LIMIT '.$many;
            $tops = $pdo->query($sql);
            foreach ($tops as $row) {
			    $top[$row['user_id']] = $row['count_id'];
			}

			return $top;
		}


		/**
		 * Retourne le nombre de minutes completées dans une heure donnée pour un utilisateur.
		 * @param string $heure
		 * @param string $user
		 * @return int
		 */
		public static function hourCompleted($heure , $user) {

			$pdo = $GLOBALS['pdo'];

			$sql = 'SELECT id FROM pdm_minutes WHERE user_id = :user_id AND minute LIKE ":heure%"';
            $sth = $pdo->prepare($sql);
            $sth->execute([':user_id' => $user, ':heure' => $heure]);

			return $sth->rowCount();

		}

	}