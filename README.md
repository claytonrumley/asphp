# asphp
My ASP-like PHP framework, designed around a not-so-strict MVC model with inherent AJAX support

## How does it work?


## How to use it

Copy the contents to a non-public location in your project.

### Create your application

You need to create a class that inherits from `\digifi\asphp\Application`. But first you need a place to put it. Create a folder to house your base classes (it does not have to be in a publicly-accessible location). Create a file in that folder for the application class (e.g. "MyApplication.php"), which should look something like this:

~~~~~

<?php


class MyApplication extends \digifi\asphp\Application 
{
    public function GetContentPath() 
    {
        //The folder that will contain your pages and master pages
        return "/path/to/content";
    }
}

~~~~~

### Direct all requests to index.php

For Apache servers, create the following .htaccess file in your www root folder:

~~~~~
# Run PHP 7.2
# https://tickets.suresupport.com/faq/article-1138/en/php_version
AddHandler application/x-httpd-php7 .php

# Enable rewrite engine 
RewriteEngine On

RewriteBase /

RewriteCond %{ENV:REDIRECT_STATUS} ^$
RewriteRule (.*) /index.php [L]

~~~~~

### Create your index.php file

~~~~~
<?php

require "/path/to/digifi/asphp_autoload.php";
require "/path/to/MyApplication.php";

$myapp = new MyApplication();

$myapp->Run();  
~~~~~



This is currently a work-in-progress. There is still much to do. Don't use this yet.


## Why did I create this?
I've been writing web applications for almost 20 years, in both PHP and .NET. My problem is most frameworks and design patterns (like strict MVC) is that while they're very powerful they require so many layers that it becomes a confusing mess. I'm not saying it's bad to completely separate your database layer from your data access layer from your database object layer, from you model layer, but you're doing a HUGE amount of work to mitigate what has (in my experience) been a very small risk.

