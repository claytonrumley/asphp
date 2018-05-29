<?php

namespace digifi\asphp;

final class Type extends BaseObject
{
    private $reflectedClass;    


    private function __construct() 
    {         
    }
    
    public static function Exists($typeName)
    {
        $result = true;
    
        try {
            $dummy = new \ReflectionClass($typeName);
        } catch (\ReflectionException $ex) {
            $result = false; 
        }
        
        return $result;
    }
    
    public function GetCustomAttribute($attributeName) 
    {
        $attributes = $this->GetCustomAttributes($attributeName, $inherit = true);
        
        if(count($attributes) == 0) return null;
        
        return $attributes[0];
    }
    
    public function GetCustomAttributes($attributeName = '', $inherit = true) 
    {
        $attributes = [];
        
        $class = $this->reflectedClass;
        
        //Start at this class and work our way up through the hierarchy:
        while($class) {
            //Extract attributes from the doc comments
            $comments = new _String($class->getDocComment());
            
            
            if($comments->Value != '') {
                $matches = $comments->Matches('/^\s*\*\s+\<(\w+)\s*\((.+)\)\s*\>\s*\n/m');
               
                foreach($matches as $m) {  
                    if($attributeName == '' || $attributeName == $m[1][0]) {
                        $attrClass = null;

                        //Check local namespaces first:
                        if (Type::Exists($m[1][0] . 'Attribute')) {
                            $attrClass = new \ReflectionClass($m[1][0] . 'Attribute');
                        //Check digifi\asphp namespace:                            
                        } elseif (Type::Exists('\\digifi\\asphp\\' . $m[1][0] . 'Attribute')) {                        
                            $attrClass = new \ReflectionClass('\\digifi\\asphp\\' . $m[1][0] . 'Attribute');
                        } else {
                            Throw new \Exception("Attribute type " . $m[1][0] . ' was not found. Are you missing a class called ' . $m[1][0] . 'Attribute?');                                                       	 
                        } 
                        
                        //Attempt to instantiate the attribute with the parameters passed to it
                        $args = null;
                        
                        if(trim($m[2][0]) != '') {
                            $args = eval('return [' . $m[2][0] . '];'); 
                        }                                            
                                  
                        //Add a new instance                                                  
                        $attributes[] = $attrClass->newInstanceArgs($args);             
                    }
                }      
                             
            }                       
        
        
            //If we're not checking inherited classes, just exit this class
            if(!$inherit) break;
            
            $class = $class->getParentClass();
        }
        
        return $attributes;
    }
    
    public function GetName() 
    {
        return $this->reflectedClass->getName();
    }
    
    public function GetParent()
    {
        $parent = $this->reflectedClass->getParentClass();
        
        if($parent) {
            $t = new Type();
        
            $t->reflectedClass = $parent;
            
            return $t; 
        }
        
        return null;
    }
    
    public function GetReflectedClass() : \ReflectionClass
    {
        return $this->reflectedClass; 
    }
    

    public static function GetTypeFromName(string $className) : Type 
    {
        $t = new Type();
        
        $t->reflectedClass = new \ReflectionClass($className);
        
        return $t;    
    }    
 
}