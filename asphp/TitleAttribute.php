<?

namespace digifi\asphp;

class TitleAttribute extends Attribute
{
    private $title;    

    public function __construct($title) 
    {
        $this->title = $title;         
    }
    
    public function GetTitle() 
    {
        return $this->title;
    }
}