<?php
	/**
	*
	*	This script will boot you VPS running on the SolusVM virtualisation platform.
	*	If the VPS is already online it will not try to boot, but instead just display a message.
	*	This is free software with an open-source license.
	*	Please do not sell or re-sell this product.
	*	If you bought this product from someone, please ask your money back.
	*	If you need more info about this license, mail me at: me@jamy.be
	*
	*	@author  	Jamy Timmermans
	*	@since 		2012
	*	
	*
	*/

	//Solusvm information: Please fill in your VPS info:
	$solus = array(
		'url' => 'https://<PROVIDERURL>:5656/api/client',
		'key' => '<APIKEY>',
		'hash' => '<APIHASH>'
	);

	// Script

	function soluscurl($solus, $action, $params = array()) {
		$solus['action'] = $action;
		$post = array_merge($solus, $params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $solus['url'] . "/command.php");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$data = curl_exec($ch);
		curl_close($ch);
		 
		// Parse the returned data and build an array
		 
		preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $match);
		$result = array();
		foreach ($match[1] as $x => $y) {
			$result[$y] = $match[2][$x];
		}
		return $result;
	}

	$status = soluscurl($solus, 'status');

	if ($status['status'] == 'success') {
		if ($status['statusmsg'] == 'online') {
			echo 'VPS already online!';
		} else {
			$boot = soluscurl($solus, 'boot');
			if ($boot['status'] == 'success' && $boot['statusmsg'] == 'booted') {
				echo 'The server has been booted!';
			} else {
				echo 'there was an error booting the server: <br>';
				var_dump($boot);
			}
		}
	} else {
		echo 'There was an error connecting to the API: <br>';
		var_dump($status);
	}
	

?>