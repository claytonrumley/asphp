<?php

namespace digifi\asphp;

class BaseObject
{
    public function GetType() : Type 
    { 
        return Type::GetTypeFromName(get_class($this));
    }

    public function ToString() : string
    {
        return get_class($this);
    }
    
    public function __toString()
    {
        return $this->ToString();
    }
}