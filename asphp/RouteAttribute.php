<?

namespace digifi\asphp;

class RouteAttribute extends Attribute
{
    private $allowedRoles = null;
    private $path;    

    public function __construct($path, $allowedRoles = null) 
    {
        //echo "Route attribute instantiated: $path <br />";
        $this->path = $path;
        $this->allowedRoles = $allowedRoles; 
    }
    
    public function GetAllowedRoles() 
    {     
        return $this->allowedRoles;
    }
    
    public function GetPath() 
    {
        return $this->path;
    }
}