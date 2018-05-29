<?php

namespace digifi\asphp;

/**
 * An HTTP request handler process a HTTP request and produces an HTTP response.
 * This interface defines the methods required to use the request handler.
 */
interface RequestHandlerInterface
{
    /**
     * Handle the request and return a response.
     */
    public function Handle(Application $application) : bool;
}
