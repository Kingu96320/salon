<?php		$contact = '8146772559';
		$content = 'hello';
	$url ='https://www.txtguru.in/imobile/api.php?username=13designstreet&password=97236194&source=ESALON&dmobile='.urlencode($contact).'&message='.urlencode($content);
	echo file_get_contents($url);
	 
 
