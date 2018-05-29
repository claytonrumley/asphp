<?php

namespace digifi\asphp;

class DirectFileHandler extends BaseObject implements RequestHandlerInterface 
{
    public function Handle(Application $application) : bool
    {
        $filename = $application->PublicFolder . $application->Request->LocalPath;
                
        if($application->Response->WriteFile($filename)) {
            return true; 
        }
        
        return false;
    }
}