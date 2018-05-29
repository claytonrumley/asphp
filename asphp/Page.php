<?php

namespace digifi\asphp;

class Page extends EndPoint 
{
    public $AutoDetectScripts = true;
    public $AutoDetectStylesheets = true;
    public $ContentFile = null;    
    public $Master;
    public $Child;      
    public $Title;
    
    private static $ScriptsToAdd = [];
    private static $StylesheetsToAdd = [];
    
    public static function AddScript($url) 
    {
        array_push(Page::$ScriptsToAdd, array("Path" => Application::$Current->PublicFolder . '/' . $url, "Url" => $url));
    }
    
    public static function AddStylesheet($url) 
    {
        array_push(Page::$StylesheetsToAdd, array("Path" => Application::$Current->PublicFolder . '/' . $url, "Url" => $url));
    }
   
    protected function EvaluateContentFile() 
    {
        if(!$this->ContentFile) return '';
        
        $evaledContent = '';
        
        $fileContents = file_get_contents($this->ContentFile);
              
        ob_start();      
        
        if(eval("?" . ">" . $fileContents) === FALSE)
        {      
          $evaledContent = "An error has occurred on this page<br />" . ob_get_contents();
          print_r(ob_get_status());
        }
        else
        {
          $evaledContent = ob_get_contents();
        }              
        
        ob_end_clean(); 
        
        return $evaledContent; 
    }  
    
    protected function GetContent() 
    {
        $pageContent = new _String();
        $content = new _String();    
                
        if($this->ContentFile) {              
            $pageContent->Value = $this->EvaluateContentFile();      
        }
        
        if($this->Master) {            
            $masterContent = new _String($this->Master->GetContent());
            
            $matches = $masterContent->Matches("/(?P<opentag><asphp:placeholder\s*id=\"(?P<id>[^\"]+)\"\s*\/>)/i");
            
            foreach($matches as $m)
            {     
                if($lastIndex < $m['opentag'][1]) {
                    $content->Append($masterContent->Substring($lastIndex, $m['opentag'][1] - $lastIndex)); 
                }
                
                $lastIndex = $m['opentag'][1] + strlen($m['opentag'][0]);                        
             
                $id = $m['id'][0]; 
             
                $tagOpen = $pageContent->IndexOfExpression("/<asphp:content\s+id=\"$id\"\s*>/i");
                
                if($tagOpen['Start'] >= 0)
                {
                    $tagClosed = $pageContent->IndexOfExpression("/<\/asphp:content\s*>/i", $tagOpen['Start'] + $tagOpen['Length']); //stripos($pageContent, "<\/rad:content>", $tagOpen);
                    $contentStart = $tagOpen['Start'] + $tagOpen['Length'] + 1;
                    
                    if($tagClosed['Start'] < 0) {
                        $content->Append("<p style=\"background-color: #333; color: #ffa; padding: 10px;\"><strong>Radwork Error:</strong> Can't find closing tag for $id.</p>"); 
                    } else {
                        $content->Append($pageContent->Substring($contentStart, $tagClosed['Start'] - $contentStart));    	
                    }                    
                }    
                
            } 
            
            if($lastIndex < $masterContent->Length()) {
                $content->Append($masterContent->Substring($lastIndex)); 
            }        

        } else {
            $content = $pageContent;          	
        }
        
        return $content;
    }
    
    public function GetExecutingPage()
    {
        if(!$this->Child) return $this;  
    
        return $this->Child->GetExecutingPage();
    }    
    
    public static function GetScriptTags() 
    {
        $tags = new _String();
    
        foreach(Page::$ScriptsToAdd as $script) {   
            $tags->Append('<script src="' . $script['Url'] . '?q=' . filemtime($script['Path']) . '"></script>');
        }
    
        return $tags->Value;
    }
    
    public static function GetStyleTags() 
    {
        $tags = new _String();
    
        foreach(Page::$StylesheetsToAdd as $stylesheet) {                
            $tags->Append('<link rel="stylesheet" href="' . $stylesheet['Url'] . '?q=' . filemtime($stylesheet['Path']) . '">');
        }
    
        return $tags->Value;
    }  
    
    public function OnInit() 
    {         
        if(file_exists($this->Application->GetContentPath() . '/' . get_class($this) .'.htm')) {
            $this->ContentFile = $this->Application->GetContentPath() . '/' . get_class($this) .'.htm';
        }
    
    
        $title = $this->GetType()->GetCustomAttribute("Title");
        
        if($title) {
            $this->Title = $title->GetTitle(); 
        }
        
        $master = $this->GetType()->GetCustomAttribute("Master");
            
        if($master && $master->GetMasterPage() != '') {                                        
            include_once($this->Application->GetContentPath() . '/' . $master->GetMasterPage() . '.htm.php');
        
            $masterClass = new \ReflectionClass($master->GetMasterPage());
      
            $this->Master = $masterClass->newInstance();         
            $this->Master->Child = $this;
            
            $this->Master->Initialize($this->Application);                       
        }
        
        //Auto detect scripts for this page, if enabled:
        if($this->Application->AutoDetectScripts && $this->AutoDetectScripts) {
            $folderArray = null;
            
            if(is_array($this->Application->ScriptsFolder)) {
                $folderArray = $this->Application->ScriptsFolder;                    
            } else {
                $folderArray = [$this->Application->ScriptsFolder];                             
            }
            
            foreach($folderArray as $searchFolder) {
                if(file_exists($this->Application->PublicFolder . '/' . $searchFolder . '/' . get_class($this) . '.js')) {
                    Page::AddScript($searchFolder . '/' . get_class($this) . '.js');
                    break; 
                } 
            }
        }
    }   
    
    public function Load()
    {
        $this->Response->SetHeader("Content-Type", "text/html");                               
        $this->Response->Write($this->GetContent());
    }
       
}