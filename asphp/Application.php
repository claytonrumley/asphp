<?

namespace digifi\asphp;

abstract class Application extends BaseObject
{

    abstract public function GetContentPath();    
    
    public $AutoDetectScripts = true;
    public $AutoDetectStylesheets = true;
    
    private $middleware = [];
    private $fallbackHandler;
    
    public $PublicFolder;       
    
    public $Request;
    public $Response;
    
    /** 
    * The folder (relative to PublicFolder) that is used to search for scripts that belong to content pages.
    * Can also be an array of folders to check.
    */
    public $ScriptsFolder = ['/script', '/scripts', '/js', '/javascript'];
    
    /** 
    * The folder (relative to PublicFolder) that is used to search for stylesheets that belong to content pages.
    * Can also be an array of folders to check.
    */
    public $StylesheetsFolder = ['/style', '/styles', '/css'];
    
    
    public static $Current;
    
    public function __construct()
    {
        Application::$Current = $this;
    
        $this->Request = new Request();
        $this->Response = new Response();
            
        $this->AddDefaultHandlers();               
    }
    
    public function AddHandler(RequestHandlerInterface $middleware)
    {
        $this->middleware[] = $middleware;
    }
    
    protected function AddDefaultHandlers() {
        $this->fallbackHandler = new NotFoundHandler();
        
        $this->AddHandler(new ContentHandler);
        $this->AddHandler(new DirectFileHandler); 
    }
    
    public static function GetCacheFolder() 
    {
      $folder = dirname(__FILE__) . '/.asphpcache';
      
      //Ensure directory exists:
      if (!file_exists($folder)) {
          mkdir($folder, 0777, true);
      }
          
      return $folder; 
    }
    
    public function SetFallbackHandler(RequestHandlerInterface $newFallback) {
        $this->fallbackHandler = $newFallback; 
    }
        
    public function Run() 
    {
        //Default the PublicFolder location to the current working directory:
        if(!$this->PublicFolder) $this->PublicFolder = getcwd();
    
        while(count($this->middleware) > 0) {    
            if($this->middleware[0]->Handle($this)) {
                return true; 
            } else {
                $middleware = array_shift($this->middleware);      	
            }             
        }    
        
        return $this->fallbackHandler->Handle($this);
    }     
}

