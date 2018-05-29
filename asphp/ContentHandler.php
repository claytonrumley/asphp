<?php

namespace digifi\asphp;

final class ContentHandler extends BaseObject implements RequestHandlerInterface
{    
    private $Routes = array();

    public function __construct()
    {
        $this->InitializeRoutes();    
    }
    
    public function AddRoute($path, $contentName, $allowedRoles = null) {
    
      $matches = array();   
      $routeDataNames = array();   
      $expression = '^';
        
      preg_match_all("/\{([^}]+)\}/im", $path, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      
      $lastIndex = 0;
      foreach($matches as $m) {
        if($m[0][1] > $lastIndex) {
          $expression .= preg_quote(substr($path, $lastIndex, $m[0][1] - $lastIndex), '/'); 
        }
        
        $expression .= "([^\/]+)";
        
        array_push($routeDataNames, $m[1][0]);
        
        $lastIndex = $m[0][1] + strlen($m[0][0]);              
      }
      
      if($lastIndex < strlen($path)) {
        $expression .= preg_quote(substr($path, $lastIndex, strlen($path) - $lastIndex), '/'); 
      }
      
      $expression .= '$';    
      
      array_push($this->Routes, array("Path" => $path, 
        "Expression" => $expression,
        "RouteDataNames" => $routeDataNames,
        "EndPoint" => $contentName, 
        "AllowedRoles" => $allowedRoles));    
    }  

    public function Handle(Application $application) : bool 
    {
        //Determine the route to follow:
        foreach($this->Routes as $route) {    
            $matches = array();
            
            preg_match_all(
                "/" . $route['Expression'] . "/im",
                $application->Request->LocalPath, 
                $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE
            );            

            if(count($matches) > 0) {
                $application->Request->IsRouted = true;            
  
                $this->Route = $route;
  
                for($m = 1; $m < count($matches[0]); $m++) {          
                    $application->Request->RouteData[$route['RouteDataNames'][$m - 1]] = $matches[0][$m][0];
                }
  
                //break on first find!
                break; 
            }
        }  
        
        if($application->Request->IsRouted) {
            $isSuccessful = false;
        
            //Instantiate the page and let 'er rip:
            $endpoint = $application->GetContentPath() . '/' . $this->Route['EndPoint'];

            if(file_exists($endpoint . '.htm')) {
                $pageInstance = null;
                           
                if(file_exists($endpoint . '.htm.php')) {
                    //If there is, include it:
                    include_once($endpoint . '.htm.php');
                  
                    $class = new \ReflectionClass($this->Route['EndPoint']);
                    $application->Request->EndPoint = $class->newInstance();
                } else {
                    $application->Request->EndPoint = new Page(); 
                }                                                                            
            } elseif (file_exists($endpoint . '.php')) {
                include_once($endpoint . '.php');
                
                $class = new \ReflectionClass($this->Route['EndPoint']);
                $application->Request->EndPoint = $class->newInstance();      
            }
            
            if($application->Request->EndPoint) {
                $application->Request->EndPoint->Initialize($application);                    
                $application->Request->EndPoint->Load();
                
                $isSuccessful = true;            
            }
        
            return $isSuccessful; 
        }
        
        return false;        
    }
    
    private function InitializeRoutes() 
    {
        //Do we need to rescan all the classes?
        $rescanRequired = false;
        $contentFolder = Application::$Current->GetContentPath();
               

        if(file_exists(Application::GetCacheFolder() . '/.routes')) {
            //Get the last write time of the cache file and see if any .htm.php files are newer:
            $lastCacheWrite = filemtime(Application::GetCacheFolder() . '/.routes');
          
            $files = array_diff(scandir($contentFolder), array('..', '.'));
          
            foreach($files as $f) {
                $info = pathinfo($f);         
                if($info['extension'] == 'php') {
                    if(filemtime($contentFolder . '/' . $f) > $lastCacheWrite) {
                        $rescanRequired = true;
                        break;          
                    }          
                } 
            }
           
        } else {
          $rescanRequired = true;
        }
        
        if($rescanRequired) {
            //Lock the cache file: 
            $fp = fopen(Application::GetCacheFolder() . '/.routes', "w+");
      
            flock($fp, LOCK_EX);
      
            $files = array_diff(scandir($contentFolder), array('..', '.'));
      
            foreach($files as $f) {        
                $info = pathinfo($f);         
          
                if($info['extension'] == 'php') {
                    $name = new _String($info['filename']);
                    $className = $name->Split("/\./")[0];
                    
                    include_once($contentFolder . '/' . $f);
                    
                    $t = Type::GetTypeFromName($className);
                
                    while($t) 
                    {
                        foreach($t->GetCustomAttributes("Route") as $route) {
                            $this->AddRoute($route->GetPath(), $className, $route->AllowedRoles);  
                        }
                                
                        $t = $t->GetParent();
                    }               
                } 
            }
        
            fwrite($fp, json_encode($this->Routes));
        
            flock($fp, LOCK_UN);
            fclose($fp);           
        } else {
            //Load the cache:            
            $fp = fopen(Application::GetCacheFolder() . '/.routes', "r");
        
            flock($fp, LOCK_SH);
                                        
            $this->Routes = json_decode(fread($fp, filesize(Application::GetCacheFolder() . '/.routes')), true);
        
            flock($fp, LOCK_UN);
        
            fclose($fp);	
        }
    }
}