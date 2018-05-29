<?

namespace digifi\asphp;

class EndPoint extends BaseObject 
{ 
    public $Application;    
    public $Request;
    public $Response;         
    
    public final function Initialize(Application $application) 
    {
        $this->Application = $application; 
        $this->Request = $application->Request;
        $this->Response = $application->Response;
        
        $this->OnInit();
    }
    
    public function OnInit() 
    { 
        //Child classes will do something here
    }
    
    public function Load()
    {
        $this->Response->SetHeader("Content-Type", "text/plain");
        $this->Response->Write("Nothing to see here.");
    }
}