<?php
class txtExtract {
	
	private $orderedBounds = array();
	private $txtBounds = array();
	private $txtDoc = "";
	private $output = array();
	private $STARTBOUND = "__START__:";
	private $ENDBOUND = ":__END__";
		
	public function __construct($doc = null, $bounds = array())
	{
		$this->txtBounds = $bounds;
		$this->txtDoc = $doc;
	}
	
	function addBound($bound)
	{
		if (is_array($bound)){
			foreach($bound as $k=>$v){
				$this->txtBounds[] = $v;
			}
		}else{
			$this->txtBounds[] = $bound;
		}
		return true;
	}
	
	function rmBound($bound)
	{
		if (is_array($bound)){
			$this->txtBounds = array_diff($this->txtBounds, $bound);
		}else{
			$nbound = array($bound);
			$this->txtBounds = array_diff($this->txtBounds, $nbound);
		}
		return true;
	}

	function boundList($boundsList, $separator="|"){
		$this->txtBounds = explode ($separator, $boundsList);
	}

	function setDoc($input){
		$this->txtDoc = $input;
	}

	/***
	*	Will sort the bounds array according to document contents so we have them ordered 
	*
	*/
	public function chkBoundsLocs(){
		foreach($this->txtBounds as $k => $v){
			$i = mb_stripos($this->txtDoc, $v);
			if ($i !== false)
				$this->orderedBounds[$i] = $v;
		}
	}

	public function sortBoundsLocs(){
		/***
		* this will sort the bound items according their position in the document
		*/
		ksort($this->orderedBounds);		
	}

	public function addStartEndBounds(){
		/***
		* Start and End of document bound positions...
		*/
		$this->orderedBounds[0] = $this->;
		$e = mb_strlen($this->txtDoc);
		$this->orderedBounds[$e] = ;		
	}

	public function debug(){
//		print_r($this->txtDoc);
//		print_r($this->txtBounds);
		print_r($this->orderedBounds);
		print_r($this->output);
	}

	public function extractDataNOK($withBound=false){		
		$k = array_keys($this->orderedBounds);
		$v = array_values($this->orderedBounds);
		$s = sizeOf($k);
		for($i=0;$i<$s-1;$i++){
			$kB1 = array_shift($k);
			$vB1 = array_shift($v);
			if( $kB1 == "__START__:" ){$vB1 = "";}
			list($kB2) = array_slice($k, 0, 1, false);
			list($vB2) = array_slice($v, 0, 1, false);
			if($withBound){
				echo "--extract substr(txtDoc, $kB1, $kB2 - 1 ):".PHP_EOL;
				echo mb_substr($this->txtDoc, $kB1, $kB2-1).PHP_EOL;
				echo "....".PHP_EOL;
			}else{
				// echo "----extract substr(txtDoc, $kB1 +".mb_strlen($vB1)." $kB2 -1".PHP_EOL;
				$this->output[] = mb_substr($this->txtDoc, $kB1 + mb_strlen($vB1), $kB2-1);
			}
		}
	}

	private function extractBetween($haystack, $bound1, $bound2){
		if ( ($bound1 == $this->STARTBOUND) && ($bound2 == $this->ENDBOUND)){
			return $haystack;
		}
		
		if ($bound1 == $this->STARTBOUND){
			list($o) = explode($bound2, $haystack);
			return $o;		
		}
		
		if ($bound2 == $this->ENDBOUND){
			list($nop, $o) = explode($bound1, $haystack);
			return $o;		
		}

		list($o) = explode($bound2, $haystack);
		list($nop, $o) = explode($bound1, $haystack);
		return $o;
	}

	public function extractData(){
		$k = array_keys($this->orderedBounds);
		$v = array_values($this->orderedBounds);
		$S = sizeOf($v);
		for ($i=0;$i<S-1;$i++){
			echo $v[$i]. "->".$v[$i+1].PHP_EOL;
		}
	}
	
		
} // end of class 
