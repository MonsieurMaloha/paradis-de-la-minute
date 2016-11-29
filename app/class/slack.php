<?php
/*
 * Gestion des échanges avec slack.
 */ 

	class slack {

		/**
		 * Retourne le token de l'application
		 * @return string
		 */
		public static function getToken() {
			return $GLOBALS['ini_settings']['token'];
		}


		/**
		 * Envoie une notification simple à Slack
		 * @param string $text
		 * @param string $channel
		 * @return string
		 */
		public static function sendNotification($text , $channel = 'in_channel') {

            return '{
                "response_type" : "'.$channel.'",
			    "text": "'.$text.'"
			}';

		}


		/**
		 * Envoie une notification avec image à Slack
		 * @param string $text
		 * @param string $channel
		 * @return string
		 */
		public static function sendImageNotification($text , $image , $channel = 'in_channel') {

			return '{
				"response_type" : "'.$channel.'",
                "text": "'.$text.'",
			    "attachments": [
			        {
			            "image_url": "'.$image.'"
			        }
			    ]
			}';

		}

/*
		// @todo
		public static function sendLongtextNotification($text , $text2 , $channel = 'in_channel') {

            return '{
                "response_type" : "'.$channel.'",
			    "text": "'.$text.'",
			    "attachments": [
			        {
			            "fallback": "Required plain-text summary of the attachment.",
			            "color": "#36a64f",
			            "pretext": "PDM documentation",
			            "fields": [
			                {
			                    "title": "Priority",
			                    "value": "High",
			                    "short": false
			                }
			            ],
			            "footer": "Malou corporation",
			            "footer_icon": "https://platform.slack-edge.com/img/default_application_icon.png",
			            "ts": 123456789
			        }
			    ]
			}';

		}
*/


	}