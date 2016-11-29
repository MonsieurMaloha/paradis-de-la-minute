<?php
/*
 * gestion des messages
 */ 

	class message {

		/**
		 * Retourne un message du type passé.
		 * @param string $type
		 * @param array $options
		 * @return string
		 */
		public static function getMessage($type , $options = []) {

			$texts = self::getText($type);
			$icons = self::getIcone($type);

			$text = $icon = '';
			if (count($texts) > 0) {
				$key_text = array_rand($texts);
				$text= $texts[$key_text];

				if (is_array($options) && count($options) > 0) {
					$text = self::replaceOptions($text , $options);
				}
			}

			if (count($icons) > 0) {
				$key_icon = array_rand($icons);
				$icon= ':'.$icons[$key_icon].':';
			}

			return self::containIcon($text) ? $text : $icon . $text;

		}


		/**
		 * Retourne un message du type passé.
		 * @param string $type
		 * @return string
		 */
		public static function getText($type) {
			return self::getDbInfo($type , 'text');
		}


		/**
		 * Retourne une icone du type passé.
		 * @param string $type
		 * @return string
		 */
		public static function getIcone($type) {
			return self::getDbInfo($type , 'icon');
		}


		/**
		 * Récupère tous les messages et les icones d'un type en base
		 * @param string $type
		 * @param string $format
		 * @return array
		 */
		public static function getDbInfo($type , $format) {
			
            $pdo = $GLOBALS['pdo'];
            $sql = 'SELECT message FROM message WHERE type = :type AND format = :format';

            $sth = $pdo->prepare($sql);
            $sth->execute([':type' => $type , ':format' => $format]);
            $results = $sth->fetchAll();
            
            $return = [];
            if (is_array($results) && count($results) > 0) {
            	foreach($results as $result) {
            		$return[] = $result['message'];
            	}
            }

            return $return;
		}


		/**
		 * Retourne le texte de l'achievment passé en paramètre.
		 * @param string $type_achievment
		 * @return string
		 */
		public static function getAchievment($type_achievment) {

			$pdo = $GLOBALS['pdo'];
            $sql = 'SELECT message FROM message WHERE achievment = :achievment';

            $sth = $pdo->prepare($sql);
            $sth->execute([':achievment' => $type_achievment]);

            $result = $sth->fetch();
            return $result['message'];
		}


		/**
		 * Change les informations dynamiques dans un texte
		 * @param string $text
		 * @param array $options
		 * @return string
		 */
		public static function replaceOptions($text , $options) {

			foreach($options as $key_option => $val_option) {
				$text = str_replace("@".$key_option."@" , $val_option , $text);
			}
			return $text;
		}



		/**
		 * Permet de savoir si on ajoute une icone aléatoire ou pas
		 * @param string $text
		 * @return boolean
		 */
		public static function containIcon($text) {
			return $text[0] == ':' ? true : false;
		}



		
		/**
		 * Retourne un container avec une icone passée.
		 * @return string
		 */
		public static function container( $icon ) {

			$ligne_container = str_repeat(':'.$icon.':', 22)." \n";

			return "
ligne_container
ligne_container
%message%
ligne_container
ligne_container";

		}


		/**
		 * Retourne la mini documentation du jeu
		 * @todo à améliorer
		 * @return string
		 */
        public static function info() {

            return '{
                "response_type" : "in_channel",
			    "attachments": [
			        {
			            "fallback": "Required plain-text summary of the attachment.",
			            "color": "#36a64f",
			            "pretext": "Le paradis de la minute : documentation :clipboard:",
			            "fields": [
                            {
			                    "title": "add",
			                    "value": "Add a minute to your pocket.",
			                    "short": false
			                },
                            {
			                    "title": "completed [name]",
			                    "value": "Percentage of game progress.",
			                    "short": false
			                },
                            {
			                    "title": "count [name]",
			                    "value": "How many minutes in the collection ? Try it.",
			                    "short": false
			                },
                            {
			                    "title": "hour [hour]",
			                    "value": "List of minutes collected and not collected.",
			                    "short": false
			                },
                            {
			                    "title": "jingle",
			                    "value": "Jingle !!.",
			                    "short": false
			                },
                            {
			                    "title": "thetime",
			                    "value": "Give the time with style !!.",
			                    "short": false
			                },
                            {
			                    "title": "top [number]",
			                    "value": "The top [number] ranking.",
			                    "short": false
			                },
                            {
			                    "title": "total",
			                    "value": "All the minutes collected !!.",
			                    "short": false
			                },
			            ],
			            "footer": "Malou corporation",
			            "footer_icon": "https://platform.slack-edge.com/img/default_application_icon.png",
			            "ts": 123456789
			        }
			    ]
			}';

		}





	}