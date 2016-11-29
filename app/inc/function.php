<?php

	function dd($data) {
		dump($data);
		die();
	}


	function dump($data) {
		echo '<pre>';
		var_dump($data);
        echo '</pre>';
	}

	function numberize($numb) {

		switch($numb) {
			case 1:
				return ":one:";
				break;
			case 2:
				return ":two:";
				break;
			case 3:
				return ":three:";
				break;
			case 4:
				return ":four:";
				break;
			case 5:
				return ":five:";
				break;
			case 6:
				return ":six:";
				break;
			case 7:
				return ":seven:";
				break;
			case 8:
				return ":eight:";
				break;
			case 9:
				return ":nine:";
				break;
			case 10:
				return ":one::zero:";
				break;
			default:
				return ":zero:";
				break;
		}

	}