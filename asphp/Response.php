<?

namespace digifi\asphp;

class Response {
    public $NoCache = false;
    private $Headers = array();          
    private $HeadersSent = false;
  
    public $ResponseCode = 200;
    
    function __construct() {
        
    }
    
    public function SetHeader($name, $value)
    {
        if($this->HeadersSent) throw new \Exception("You cannot set headers after they've already been sent");
        $this->Headers[$name] = $value;
    }

    public function GetHeader($name)
    {
        if (array_key_exists($name, $this->Headers)) {
                return $this->Headers[$name];
        }

        return null;
    }
    
    public function Redirect($url)
    {
        header("Location: $url");
        ob_end_flush(); // All output buffers must be flushed here
        flush();        // Force output to client
    }
    
    protected function WriteHeaders() 
    {
        if($this->HeadersSent) return;
        
        http_response_code($this->ResponseCode);
    
        //No cache
        if($this->NoCache) {
            $this->SetHeader('Expires', 0);
            $this->SetHeader('Cache-Control', 'must-revalidate');
            $this->SetHeader('Pragma', 'public');
        }
    
        foreach($this->Headers as $key => $value) {
            header("$key: $value");
        }
        $this->HeadersSent = true;
    }
    
    public function Write($value) { 
        if(!$this->HeadersSent) $this->WriteHeaders();
        
        echo $value;
    }
    
    public function WriteFile($filename) { 
        //From: https://stackoverflow.com/questions/2882472/php-send-file-to-user             
        if(file_exists($filename) && !is_dir($filename)) {
            //Get file type and set it as Content Type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filename);
            
            switch(pathinfo($filename, PATHINFO_EXTENSION)) {
                case 'css':
                    $mimeType = 'text/css';
                break; 
            }
            
            $this->SetHeader('Content-Type', $mimeType);
            
            finfo_close($finfo);    

            //Define file size
            $this->SetHeader('Content-Length', filesize($filename));
    
            ob_clean();
            flush();
            $this->WriteHeaders();
            readfile($filename); 
            return true;
        }                     
        
        return false;
    }
}