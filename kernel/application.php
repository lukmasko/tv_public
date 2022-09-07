<?php

namespace Kernel\Application;
use Kernel\Controller;
use Kernel\Route;
use Kernel\Auth\Auth;

use App\Controllers;

class Application
{
    private $requestMethod;
    private $requestURI;
    private $runClass;
    private $runFunction;

    private $runArgs = array();

    public function __construct()
    {
        $this->includeFiles();
        $this->getRequestInfo();
    }

    private function getRequestInfo()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? NULL;

        $uri = explode('?', $_SERVER['REQUEST_URI']);
        $this->requestURI = $uri[0] ?? NULL;

        $this->getObject();
    }

    public function getObject() : void 
    {
        $routing = Route\Route::get();

        foreach($routing as $route)
        {
            if( ($this->checkMethod($route['method'])) && 
                ($this->checkPath($route['path'])) )
            {
                if($this->checkRole($route['role']) === false){
                    $this->runClass = new Controllers\NoPermissionController();
                    $this->runFunction = 'index';
                    return;
                }

                $class = 'App\\Controllers\\'. $route['controller'];

                if(class_exists($class)){
                    $this->runClass = new $class();
                    $this->runFunction = $route['function'];
                    return;
                }
                break;
            }
        }

        $this->runClass = new Controllers\Error404Controller();
        $this->runFunction = 'index';
    }

    private function checkRole(string $role) : bool
    {
        return true;
    }

    private function checkMethod($method) : bool 
    {
        return ( strcmp(strtolower($method), strtolower($this->requestMethod)) == 0 ) ? true : false;
    }

    private function checkPath($path) : bool
    {
        $uri = explode('/', $this->requestURI);
        $route = explode('/', $path);

        if( count($uri) != count($route) )
            return false;

        for($i=0; $i<count($uri); $i++)
        {
            if( !empty($route[$i]) && $route[$i][0] == '{' && substr($route[$i], -1) == '}' ){
                $this->runArgs[] = $uri[$i];
                continue;
            }

            if(strcmp($uri[$i], $route[$i]) != 0)
                return false;
        }
        return true;
    }

    public function run() : void
    {
        //$this->runClass->{$this->runFunction}(/*explode(',', $this->run_args_str)*/20, 4098);
        call_user_func_array(array($this->runClass, $this->runFunction), $this->runArgs);
    }

    private function includeFiles()
    {
        $this->includeFolderEx('../kernel/', NULL, array('bootstrap.php', 'application.php'));
        include_once '../app/routes/web.php';
        $this->includeFolder('../app/db/', 'DB');
        $this->includeFolder('../app/controllers/', 'Controller');
        $this->includeFolder('../app/models/', 'Model');
    }

    private function includeFolder(string $dir, string $sufix) : void 
    {
        $files = scandir($dir, SCANDIR_SORT_ASCENDING);
        $sufixLen = strlen($sufix);
        
        foreach($files as $file){
            $f = explode('.php', $file);
            if(strcmp(substr($f[0], -$sufixLen), $sufix) != 0)
                continue;
            include_once $dir.$file;
        }
    }

    private function includeFolderEx(string $dir, array $adds=NULL, array $expects=NULL) : void 
    {
        $files = scandir($dir, SCANDIR_SORT_ASCENDING);

        foreach($files as $file){

            if(strstr($file, '.php') === false)
                continue;

            foreach($expects as $expect){
                if(strcmp($file, $expect) == 0)
                    continue 2;
            }

            include_once $dir.$file;
        }
    }
}