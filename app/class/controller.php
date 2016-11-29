<?php
/*
 * Gestion des appels des actions par les utilisateurs.
 */ 
	class controller {

		/**
		 * @var array données de la variable $_POST
		 */
		private $post;

		/**
		 * @var array paramètres passés par l'utilisateur (0 => action , [1 => parmètre de l'action])
		 */
		private $params;

		/**
		 * @var string user ID fournit par Slack (string)
		 */
		private $user_id;

		/**
		 * @var string user name fournit par Slack (format @xxxx)
		 */
		private $user_name;

		/**
		 * @var string heure au format Hi
		 */
		private $time_minute;

		/**
		 * @var string heure au format H:i
		 */
		private $text_minute;


		/**
		 * @var string heure au format H:i:s
		 */
		private $full_time;

		/**
		 * Consctruct le controller
		 * @param array $post
		 */
		public function __construct($post) {
			$this->post = $post;
			$this->user_id = $post['user_id'];
			$this->user_name = $post['user_name'];
			$this->params = self::getParameters($post['text']);

			$this->time_minute = date('Hi');
			$this->text_minute = date('H:i');
			$this->full_time = date('H:i:s');
		}

		/**
		 * Scinde les paramètres envoyés par un utilisateur.
		 * Le paramètre 0 correspond à l'action souhaité par l'utilisateur.
		 * Les paramètres suivants sont optionnels.
		 * @param string $parameters
		 * @return array
		 */
		public static function getParameters($parameters) {
			return explode(' ', $parameters);
		}


		/**
		 * Méthode de rendu de l'application.
		 * Le paramètre 0 correspond à l'action souhaité par l'utilisateur.
		 * Les paramètres suivants sont optionnels.
		 * @param string $parameters
		 * @return array
		 */
		public function render() {
			$method = strtolower($this->params[0]);
			// Raccourci vers la méthode "add", si aucune action n'est définie.
			if (trim($method) == '') $method = 'add';

			if (method_exists($this , $method))
				return $this->$method();
			else
				return $this->undefinedMethod();
		}

		/**
		 * Méthode d'ajout d'une minute.
		 * @return string
		 */
		private function add() { 
			// Récupération de l'instance de l'utilisateur.
			$user = new user( $this->user_id , $this->user_name );
			$minute = $this->time_minute;
			// Tentative de création de la minute
			// ou
			// Minute déjà existante.
   			if (pdm::add($minute , $user->user['id'])) {
   				// Existe t-il un achievment pour la minute récoltée ?
				$achievment = new achievment($minute , $user->user['id']);
                $message = $achievment->getAchievment();
				if ($message == false) {
                    $message = message::getMessage('success').' *('.$this->text_minute.')*.';
				}
				return slack::sendNotification($message);
			} else {
				$message = message::getMessage('already');
				return slack::sendNotification($message);
			}

		}

		/**
		 * Compte le nombre de minutes collectées pour un utilisateur (user courant par défaut).
		 * @return string
		 */
		private function count() {
			// Si la requête est effectuée pour un utilisateur précis, on charge celui-ci.
			// ou
			// On charge l'utilisateur courant.
			if (!empty($this->params[1])) {
				$user = user::findUser($this->params[1]);

				if ($user == false) {
					$message = message::getMessage('whois' , ['name' => $this->params[1]]);
					return slack::sendNotification($message);
					}
				$user_id = $user['id'];

			} else {
                $user = new user($this->user_id , $this->user_name);
				$user_id =$user->user['id'];
			}
            
			$count= pdm::count($user_id);
			$message = message::getMessage('count_collected' , ['count' => $count]);
			return slack::sendNotification($message);
		}

		/**
		 * Retourne le nombre de minutes total collectées par tous les joueurs.
		 * @return string
		 */
		private function total() {
			$count = pdm::total();
			$message = message::getMessage('total' , ['user_name' => $this->user_name , 'count' => $count]);
			return slack::sendNotification($message);
		}

		/**
		 * Fonction cachée pour la commande "PZ". (easter egg).
		 * @return string
		 */
		private function pz() {
			$message = message::getMessage('pz');
         	return slack::sendNotification($message);
		}

		/**
		 * Retourne l'heure courante.
		 * @return string
		 */
		private function thetime() {
			$message = message::getMessage('thetime' , ['time' => $this->full_time]);
         	return slack::sendNotification($message);
		}

		/**
		 * Retourne les paroles du jingle du jeu.
		 * @return string
		 */
		private function jingle() {
   			$message = message::getMessage('jingle');
         	return slack::sendNotification($message);
		}

		/**
		 * Fonction cachée pour la commande "BRD". (easter egg).
		 * @return string
		 */
		private function brd() {
   			$message = message::getMessage('brd');
     		return slack::sendNotification($message);
		}

		/**
		 * Retourne la liste des minutes acquises et non acquises pour une heure donnée (par défaut l'heure courante).
		 * @return string
		 */
		private function hour() {
            $user = new user($this->user_id , $this->user_name);
			$hour = empty ($this->params[1]) ? date('H') : $this->params[1];
   			$minutes = pdm::hour($user->user['id'] , $hour);
			$message = '';
			$i = 0;
			foreach ($minutes as $minute) {
                $message .= $minute."    ";
				$i++;
				if ($i%10 == 0)
					$message .= "\n";
			}
			$message = str_replace( '%message%' , $message , message::container('heavy_minus_sign') );
         	return slack::sendNotification($message , 'ephemeral');
		}

		/**
		 * Retourne le pourcentage d'avancement du jeu pour un utilisateur (utilisateur courant par défaut).
		 * @return string
		 */
		private function completed() {
			// Si la requête est effectuée pour un utilisateur précis, on charge celui-ci.
			// ou
			// On charge l'utilisateur courant.
			if (!empty($this->params[1])) {
				$user = user::findUser($this->params[1]);

				if ($user == false) {
					$message = message::getMessage('whois' , ['name' => $this->params[1]]);
					return slack::sendNotification($message);
				}
				$user_id = $user['id'];

			} else {
	            $user = new user($this->user_id , $this->user_name);
				$user_id =$user->user['id'];
			}

			$pourcent = (pdm::count($user_id)/1440)*100;
			// Si le pourcentage est inférieur à 1 on augmente la précision
			// ou
			// On arrondit à l'entier.
			if ($pourcent < 1) {
	            $pourcent = round($pourcent,5);
			}
			else
	            $pourcent = round($pourcent);

			$numberHour= ceil((ceil($pourcent)/100)*12);
			if ($numberHour==0) $numberHour = 1;
	   		$message = message::getMessage('completed' , ['numberHour' => $numberHour , 'pourcent' => $pourcent ]);
	        return slack::sendNotification($message);
		}


		/**
		 * Retourne le classement des X top utilisateurs.
		 * @return string
		 */
		private function top() {
			$many = empty($this->params[1]) ? 5 : (int) $this->params[1];
			$tops = pdm::top( $many );
			$message = "";
			$i = 1;
			foreach($tops as $user_id => $count) {
				$name = user::name($user_id);
				$icon = numberize($i);
				$message .= $icon .' '. $name['name'].' with *'.$count." minutes*.\n";
				$i++;
			}
         	return slack::sendNotification($message);
		}

		/**
		 * Retourne la petite documentation des méthodes existantes.
		 * @return string
		 */
		private function info() {
			return message::info();
		}

		/**
		 * Retourne un message dans le cas d'un appel de méthode inconnue.
		 * @return string
		 */
		private function undefinedMethod() {
			$message = message::getMessage('404');
			return $message;
		}
	}