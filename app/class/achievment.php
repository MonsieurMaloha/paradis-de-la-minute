<?php
/*
 * Gestion des achievments.
 * Lors de l'ajout d'une minute, tout un tas d'évènement peuvent se déclencher pour ressortir un achievment.
 */ 

	class achievment {

		/**
		 * @var string minute courante (Hi)
		 */
		private $minute;

		/**
		 * @var string User ID fournit par Slack (string)
		 */
		private $user;

		/**
		 * @var array achievment déclenché par la collection de la minute.
		 */
		private $message= [];

		/**
		 * @var array liste des minutes considérées comme magiques.
		 */
		private $magic = ['0000' , '1111' , '2222'];

		/**
		 * @var array liste des minutes de naissance.
		 */
        private $birth = ['1125'];

        /**
		 * @var int nombre de minutes collectées par l'utilisateur.
		 */
		private $count_user;


		/**
		 * Consctruct le achievement
		 * @param string $minute
		 * @param int $user
		 */
		public function __construct($minute , $user) {

			$this->minute 	= $minute;
			$this->user 	= $user;

			$this->minuteSpeciale();
			$this->userEvent();

		}


		/**
		 * Retourne le texte de l'achievment si un ou plusieurs achievment disponibles.
		 * Retourne faux si aucun achievment.
		 * @return mixed (string|boolean)
		 */
		public function getAchievment() {

			if (count($this->message) > 0) {
				$key = array_rand($this->message);
				$container = $this->container();
				return str_replace( '%message%' , $this->message[$key] , $container );
			}
			return false;

		}

		/**
		 * Retourne le container de mise en forme esthétique d'un achievement.
		 * @return string
		 */
		private function container() {

			$ligne_container = str_repeat(':star:', 19)." \n";

			return "
$ligne_container
$ligne_container
%message%
$ligne_container
$ligne_container";

		}


		/**
		 * Retourne le texte associé au type d'achievment
		 * @param string $type
		 * @return string
		 */
		private function getText($type) {
			return message::getAchievment($type);
		}


		/**
		 * Effectue tous les tests sur la minute pour voir si elle est considérée comme spéciale.
		 */
		private function minuteSpeciale() {

			$methods = [
				'isSuite' 		=> 'suite',
				'isPalindrome' 	=> 'palindrome',
				'isMagic' 		=> 'magic',
				'isBirthday' 	=> 'birth',
				'aClock' 		=> 'aclock',
				'isSame' 		=> 'same'
			];

			$this->runMethods($methods);
		}


		/**
		 * Execute les méthodes de test d'achivement.
		 * @param array $methods
		 */
		private function runMethods($methods) {
			foreach ($methods as $name_method => $textAchievment) {
				if (method_exists($this , $name_method)) {
					if ($this->$name_method())
						$this->message[] = $this->getText($textAchievment);
				}
			}
		}


		/**
		 * Contrôle si la minute est une suite (ex: 12:34)
		 * @return boolean
		 */
		protected function isSuite() {

			$minute = (string) $this->minute;
			for($i = 1; $i <= strlen($minute); $i++ ) {
				if ($minute[$i] != $minute[$i-1]) return false;
			}
			return true;

		}


		/**
		 * Contrôle si les minutes sont identiques aux heures (ex: 12:12)
		 * @return boolean
		 */
		protected function isSame() {

			$minute = (string) $this->minute;
			return $minute[0].$minute[1] == $minute[2].$minute[3] ? true : false;

		}


		/**
		 * Contrôle si l'heure passée est une heure pile (ex: 12h00)
		 * @return boolean
		 */
		protected function aClock() {

			$minute = (string) $this->minute;
			return $minute[2].$minute[3] == '00' ? true : false;

		}


		/**
		 * Contrôle si la minute est un palindrome (ex: 12:21)
		 * @return boolean
		 */
		protected function isPalindrome() {

            $minute = (string) $this->minute;
			if (in_array($minute , $this->magic)) return false;

			return $minute == strrev($minute) ? true : false;

		}


		/**
		 * Contrôle si la minute est une minute magique
		 * @return boolean
		 */
		protected function isMagic() {

            $minute = (string) $this->minute;
			return (in_array($minute , $this->magic)) ? true : false;

		}


		/**
		 * Contrôle si la minute est une minute d'anniversaire
		 * @return boolean
		 */
		protected function isBirthday() {

            $minute = (string) $this->minute;
			return (in_array($minute , $this->birth)) ? true : false;

		}


		/**
		 * Effectue les tests sur l'utilisateur afin de savoir si on doit déclencher un achievment (ex: 100 minutes collectés)
		 */
		private function userEvent() {

			$this->count_user = pdm::count($this->user);

			$methods = [
				'firstMinute' 					=> 'firstMinute',
				'firstHour' 					=> 'firstHour',
				'towFirstHour' 					=> 'towFirstHour',
				'threeFirstHour' 				=> 'threeFirstHour',
				'hundred' 						=> '100',
				'twoHundred' 					=> '200',
				'treeHundred' 					=> '300',
				'fourHundred' 					=> '400',
				'hourCompleted' 				=> 'hourCompleted',
				'twentyFivePourcentCompleted'	=> '25pourcent',
				'fiftyPourcentCompleted' 		=> '50pourcent',
				'seventyFivePourcentCompleted' 	=> '75pourcent'
			];
			
			$this->runMethods($methods);

		}


		/**
		 * Contrôle si la minute collectée est la première collectée.
		 * @return boolean
		 */
		protected function firstMinute() {

   			return $this->count_user == 1 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total une heure de minute
		 * @return boolean
		 */
		protected function firstHour() {

   			return $this->count_user == 60 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total deux heures de minute
		 * @return boolean
		 */
		protected function towFirstHour() {

   			return $this->count_user == 120 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total trpis heures de minute
		 * @return boolean
		 */
		protected function threeFirstHour() {

   			return $this->count_user == 180 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total 100 minutes
		 * @return boolean
		 */
		protected function hundred() {

   			return $this->count_user == 100 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total 200 minutes
		 * @return boolean
		 */
		protected function twoHundred() {

   			return $this->count_user == 200 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total 300 minutes
		 * @return boolean
		 */
		protected function treeHundred() {

   			return $this->count_user == 300 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté au total 400 minutes
		 * @return boolean
		 */
		protected function fourHundred() {

   			return $this->count_user == 400 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté une heure complète (ex: de 12:00 à 12:59)
		 * @return boolean
		 */
		protected function hourCompleted() {

            $minute = (string) $this->minute;
			$heure = $minute[0].$minute[1];
   			return pdm::hourCompleted($heure, $this->user) == 60 ? true : false;

		}


		/**
		 * Contrôle si l'utilisateur a collecté 25% des minutes.
		 * @return boolean
		 */
		protected function twentyFivePourcentCompleted() {

   			return $this->count_user == 360 ? true : false;

		}

		/**
		 * Contrôle si l'utilisateur a collecté 50% des minutes.
		 * @return boolean
		 */
        protected function fiftyPourcentCompleted() {

   			return $this->count_user == 720 ? true : false;

		}

		/**
		 * Contrôle si l'utilisateur a collecté 75% des minutes.
		 * @return boolean
		 */
        protected function seventyFivePourcentCompleted() {

   			return $this->count_user == 1080 ? true : false;

		}

	}