<?

namespace digifi\asphp;

class MasterAttribute extends Attribute
{
    private $masterPage;    

    public function __construct($masterPage) 
    {        
        $this->masterPage = $masterPage;         
    }
    
    public function GetMasterPage() 
    {
        return $this->masterPage;
    }
}