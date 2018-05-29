<?php

namespace digifi\asphp;

class NotFoundHandler extends BaseObject implements RequestHandlerInterface 
{
    public function Handle(Application $application) : bool
    {
        $application->Response->ResponseCode = 404;
        $application->Response->Write("<p>Path not found (" . $application->Request->LocalPath . ')</p>');
        $s = new _String("Testing");
        $application->Response->Write($s->Equals(new _String("Testing")) ? 'Yes' : 'no');
        
        return true;        
    }
}