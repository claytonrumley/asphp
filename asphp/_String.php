<?php

namespace digifi\asphp;

class _String extends BaseObject
{

    public $Value = '';

    public function __construct($initialValue = '') 
    {
        $this->Value = $initialValue; 
    }
  
    public function Append($s) 
    {
        if(gettype($s) == 'object' && get_class($s) == 'RadString') {
            $this->Value .= $s->Value; 
        } else {
            $this->Value .= $s;
        }
      
        return $this; 
    }
  
    public function Compare($s, $caseSensitive = true)
    {
        if($caseSensitive) {
            return strcmp($this->Value, $s); 
        } else {
            return strcasecmp($this->Value, $s);            	
        }         
    }
    
    public function Contains($s, $caseSensitive = true) 
    {
        return $this->IsMatch('/' . preg_quote($s, '/') . '/' . ($caseSensitive ? '' : 'i')); 
    }
    
    public function EndsWidth($s) : bool 
    {        
        $tester = $this->getInternalString($s);
        return $this->Right(strlen($tester)) == $tester;
    }  
    
    public function Equals($s, $caseSensitive = true) 
    {
        return ($this->Compare($s, $caseSensitive) === 0);     
    }
    
    private function getInternalString($input)
    {
        if(gettype($input) == 'string') {
            return $input;
        }
        
        return sprintf("%s", $input);
    }
    
    public function IndexOf($s, $caseSensitive = true, $startPos = 0) 
    {
        if(!$caseSensitive) return stripos($this->Value, $s, $startPos);        
    
        return strpos($this->Value, $s, $startPos);     
    }
    
    public function IndexOfExpression($expression, $startPos = 0) 
    {
        $matches = array();
        
        if(preg_match($expression, $this->Value, $matches, PREG_OFFSET_CAPTURE, $startPos)) {
            return array('Start' => $matches[0][1], 'Length' => strlen($matches[0][0]));
        }         
    
        return array('Start' => -1, 'Length' => 0);     
    }
    
    public function Insert($position, $s) 
    {
        if($position <= 0) {
            return $this->Prepend($s); 
        }
    
        if($position > $this->Length()) {
            return $this->Append($s);
        }
        
        $this->Value = $this->Left($position) . $s . $this->Right($this->Length() - $position);
            
        return $this; 
    }
    
    public function IsMatch($exp, array &$matches = null, $flags = 0, $offset = 0) 
    {
        return preg_match($exp, $this->Value, $matches, $flags, $offset); 
    }
    
    public function LastIndexOf($s) 
    {
        if(!$caseSensitive) return strripos($this->Value, $s);
        
        return strrpos($this->Value, $s);     
    }
    
    public function Matches($exp, $returnAll = true, $flags = PREG_SET_ORDER | PREG_OFFSET_CAPTURE, $offset = 0) 
    {
        $matches = array();
        
        if($returnAll) {
            preg_match_all($exp, $this->Value, $matches, $flags, $offset); 
        } else {
            preg_match($exp, $this->Value, $matches, $flags, $offset);
        }        
        
        return $matches; 
    }
    
    public function Left($count) 
    {
        return substr($this->Value, 0, $count); 
    }
    
    public function Length() 
    {
        return strlen($this->Value); 
    }
    
    public function Prepend($s)
    {
        $this->Value = $s . $this->Value; 
        return $this;
    }
    
    public function Remove($position, $count)
    {
        $this->Value = $this->Left($position) . $this->Substring($position + $count);
        return $this; 
    }
    
    public function Replace($exp, $replacement, $limit = -1, int &$count = null)
    {
        $this->Value = preg_replace($exp, $replacement, $this->Value, $limit, $count);
        return $this; 
    }
    
    public function Reverse()
    {
        $this->Value = strrev($this->Value);
        return $this; 
    }
    
    public function Right($count)
    {
        return substr($this->Value, $this->Length() - $count, $count); 
    }
    
    public function Split($exp, $limit = -1, $flags = 0)
    {
        return preg_split ($exp, $this->Value, $limit, $flags); 
    }
    
    public function StartsWith($s) : bool 
    {        
        $tester = $this->getInternalString($s);
        return $this->Left(strlen($tester)) == $tester;
    }    
    
    public function Substring($start, $length = -1)
    {
        if ($length < 0) $length = $this->Length() - $start;
        
        return substr($this->Value, $start, $length);
    }     
    
    public function ToLower()
    {
        $this->Value = strtolower($this->Value);
        
        return $this; 
    }
    
    public function ToString() : string
    {
        return $this->Value;
    }
    
    public function ToUpper()
    {
        $this->Value = strtoupper($this->Value);
        
        return $this; 
    }
    
    public function Trim($charList = " \t\n\r\0\x0B")
    {
        $this->Value = trim($this->Value, $charList);
        return $this; 
    }
    
    public function __toString()
    {
        return sprintf("%s", $this->Value);
    }

}