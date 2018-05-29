<?php

require "asphp/Autoloader.php";

$loader = new \digifi\asphp\Autoloader();

$loader->register();

$loader->addNamespace('digifi\asphp', '/home/digifi/private/maestro/digifi/asphp');