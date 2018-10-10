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
		for ($i=0;$i<$S-1;$i++){
			$this->DEBUG('betweens', $v[$i]. "->".$v[$i+1]);
			$o[] = $this->extractBetween($this->txtDoc, $v[$i], $v[$i+1]);
		}
		$this->DEBUG('output',$o);
		$this->DEBUG();
		
	}
} // end of class 
