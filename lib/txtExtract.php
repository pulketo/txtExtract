<?php
class txtExtract {
	
	private $orderedBounds = array();
	private $txtBounds = array();
	private $txtDoc = "";
	private $output = array();
	private $STARTBOUND = "__START__:";
	private $ENDBOUND = ":__END__";

	private $showStartEndBounds = false;
	private $outputKey = "BOTH";	
		
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
		$this->orderedBounds[0] = $this->STARTBOUND;
		$e = mb_strlen($this->txtDoc);
		$this->orderedBounds[$e] = $this->ENDBOUND;		
	}

	public function DEBUG($keyword=null, $what=null){
		if ($what==null){
				print_r($this->debugOut);
				return true;
		}else{
			if ($keyword==null){
				$this->debugOut[]=$what;
			}else{
				$this->debugOut[$keyword][]=$what;				
			}
		}
	}

	private function extractBetween($haystack, $bound1, $bound2){
		// start to endbound
		if ( ($bound1 == $this->STARTBOUND) && ($bound2 == $this->ENDBOUND)){
			$this->DEBUG('extractBetween', "start to end");
			return $haystack;
		}
		// start to bound		
		if ($bound1 == $this->STARTBOUND){
			list($o) = explode($bound2, $haystack);
			$this->DEBUG('extractBetween', "Start to Bound ($o)");
			return $o;		
		}
		// bound to end		
		if ($bound2 == $this->ENDBOUND){
			list($nop, $o) = explode($bound1, $haystack);
			$this->DEBUG('extractBetween', "Bound to End ($o)");
			return $o;		
		}
		// bound to bound
		$c1 = explode($bound1, $haystack);
		$c2 = explode($bound2, $c1[1]);
		$o = trim($c2[0]);
		$this->DEBUG('extractBetween', "Bound to Bound ($o)");
		return $o;
	}

	public function setOutputKey($ok){
		$ook = strtoupper(trim($ok));
		switch($ook){
			case "BOTH":
			case "NONE":
			case "LEFTBOUND":
			case "RIGHTBOUND":
				$this->outputKey=$ook;
				break;
			default:
				$this->outputKey="BOTH";
				break;
		}
	}

	public function setShowStartEndBounds($show=false){
		$this->showStartEndBounds = ($show)?true:false;
	}
	
	public function extractData(){
		$k = array_keys($this->orderedBounds);
		$v = array_values($this->orderedBounds);
		$S = sizeOf($v);
		if ( $this->showStartEndBounds ===  true){
			$ST = 0; $EN = 1;
		}else{
			$ST = 1; $EN = 2;
		}
		for ($i=$ST;$i<$S-$EN;$i++){
			$B1 = $v[$i];
			$B2 = $v[$i+1];
			$this->DEBUG('betweens', $B1. "->".$B2);
			switch($this->outputKey){
				case "LEFTBOUND":
					$INDEX = mb_substr($B1,0,120);
					$o[$INDEX] = $this->extractBetween($this->txtDoc, $v[$i], $v[$i+1]);
					break;
				case "RIGHTBOUND":
					$INDEX = mb_substr($B2,0,120);
					$o[$INDEX] = $this->extractBetween($this->txtDoc, $v[$i], $v[$i+1]);
					break;
				case "BOTH":
					$INDEX = mb_substr($B1,0,120)."->".mb_substr($B2,0,120);
					$o[$INDEX] = $this->extractBetween($this->txtDoc, $v[$i], $v[$i+1]);
					break;
				default:
					$o[] = $this->extractBetween($this->txtDoc, $v[$i], $v[$i+1]);
					break;
			}
		}
		return $o;
		// $this->DEBUG('output',$o);
	}
} // end of class 
