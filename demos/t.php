<?php
	require "../lib/txtExtract.php";
	$doc = new txtExtract();
	$doc->setDoc(file_get_contents('Clonazepam Oral.htm.txt'));	
	$doc->boundList(file_get_contents('fields.txt'), PHP_EOL);
	$doc->chkBoundsLocs();
		$doc->addStartEndBounds();
		$doc->sortBoundsLocs();
//		$doc->extractData(false, "FIRST");
//		print_r($doc->extractData(true, "BOTH"));
		$doc->setOutputKey("both");
		$doc->setShowStartEndBounds(true);
		print_r($doc->extractData());		
exit;
