<?php
/*
 * Gestion des utilisateurs.
 * Permet la gestion et la création des utilisateurs.
 */ 

	class user {

		/**
		 * @var string User ID fournit par Slack (string)
		 */
		private $slack_id;

		/**
		 * @var string User name fournit par Slack (format @xxxx)
		 */
		private $name;

		/**
		 * @var array Données du user
		 */
		private $user;

		/**
		 * Consctruct le user
		 * @param string $slack_id
		 * @param string $name
		 */
		public function __construct($slack_id , $name = '') {
			$this->name = $name;
			if (!is_null($slack_id)) {
				$this->slack_id = $slack_id;
				$this->user = $this->initUser();
			}
		}

		/**
		 * Getter user
		 * @param string $att
		 * @return mixed
		 */
		public function __get($att) {
			return $this->$att;
		}

		/**
		 * Initialise un user et tente de le créer si il est inexistant
		 * @return array
		 */
		public function initUser() {

			global $pdo;

			$sql 	= 'SELECT id , name FROM user WHERE slack_id = :slack_id';
			$sth 	= $pdo->prepare($sql);
			$sth->execute([':slack_id' => $this->slack_id]);

			if ($sth->rowCount() == 0)
				$this->addUser();

			return $sth->fetch(PDO::FETCH_ASSOC);

		}

		/**
		 * Création d'un user
		 */
		private function addUser() {
			global $pdo;

			$sql = "INSERT INTO user (slack_id, name) VALUES (:slack_id, :name)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':slack_id', $this->slack_id);
            $stmt->bindParam(':name', $this->name);

            $stmt->execute();

			if ($pdo->lastInsertId() > 0)
				$this->initUser();
		}

		/**
		 * Récupération d'un user
		 * @param string $name
		 * @return mixed [array | false]
		 */
		public static function findUser($name) {
			global $pdo;

			$name = trim( str_replace('@','',$name) );

			$sql 	= 'SELECT id , name FROM user WHERE name = :name';
			$sth 	= $pdo->prepare($sql);
			$sth->execute([':name' => $name]);

			if ($sth->rowCount() == 0)
				return false;

			return $sth->fetch();
		}

		/**
		 * Récupère le nom d'un user par son ID interne
		 * @param int $user_id
		 * @return array
		 */
		public static function name($user_id) {
            global $pdo;

			$sql 	= 'SELECT name FROM user WHERE id = :id';
			$sth 	= $pdo->prepare($sql);
			$sth->execute([':id' => $user_id]);

			return $sth->fetch(PDO::FETCH_ASSOC);
		}
	}