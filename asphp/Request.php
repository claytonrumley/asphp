<?

namespace digifi\asphp;

class Request {

  public $Host;
  public $EndPoint = null;
  public $IsSecure = 0;
  public $LocalPath;
  public $RawUri; 
  public $RouteData;
  public $IsRouted = 0;     

  function __construct() {
    //$this->Content = '';
    //$this->Route;
    $this->Host = $_SERVER['HTTP_HOST'];
    $this->LocalPath = strtok($_SERVER["REQUEST_URI"],'?');
    $this->IsSecure = (int)isset($_SERVER['HTTPS']);
    $this->RawUri = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
    //Determine the route to follow:
    /*foreach($this->Framework->Routes as $route) {
      $matches = array();
      
      preg_match_all("/" . $route['Expression'] . "/im", $this->LocalPath, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      
      if(count($matches) > 0) {
        $this->IsRouted = true;
        
        $this->Route = $route;
        
        for($m = 1; $m < count($matches[0]); $m++) {          
          $this->RouteData[$route['RouteDataNames'][$m - 1]] = $matches[0][$m][0];
        }
        
        //break on first find!
        break; 
      }
      
    }    */
  }
  
  public function Form($name) {
    return $_POST[$name]; 
  }
  
  public function QueryString($name) {
    return $_GET[$name]; 
  }

} 