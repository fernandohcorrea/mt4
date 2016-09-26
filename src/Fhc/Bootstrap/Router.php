<?php

namespace Fhc\Bootstrap;

use Fhc\Config\Loader;
use Fhc\Controller\Base;
use Fhc\Exception as Excp;

/**
 * Description of Router
 *
 * @author fcorrea
 */
final class Router
{
    const CONT_SUFIX = 'Controller';
    
    private $cfg;
    private $controller_namespace;
    private $controller;
    private $controller_obj;
    private $controller_class_name;
    private $reflectionController;
    private $action = 'index';
    private $reflectionAction;
    private $view_default;
    private $view_path;
    private $query_string;
    private $get_params;
    private $url_params;

    public function __construct()
    {
        $this->parseCfg();
        $this->parseRequestUri();
        $this->parseRequestQuery();
    }
    
    private function parseCfg()
    {
        $cfg_root = 'router';
        $this->cfg = Loader::get($cfg_root.'*');
        if(!is_array($this->cfg)){
            throw new Excp\RuntimeException('Config error router_(controller_namespace, controller_default ...)');
        }
        
        $required = [
            'controller_namespace', 'controller_default', 'action_default', 'view_default'
        ];
        
        foreach ($required as $cfg_key){
            $cfg_idx = $cfg_root . '_' .$cfg_key;
            
            if(!isset($this->cfg[$cfg_idx]) || is_null($this->cfg[$cfg_idx])){
                throw new Excp\RuntimeException("Config required[$cfg_idx]");
            }
            
            switch ($cfg_key) {
                case 'controller_namespace':
                    $this->controller_namespace = $this->cfg[$cfg_idx];
                    break;
                
                case 'controller_default':
                    $this->controller = $this->fixControllerName($this->cfg[$cfg_idx]);
                    break;
                
                case 'action_default':
                    $this->action = $this->fixActionName($this->cfg[$cfg_idx]);
                    break;
                
                case 'view_default':
                    $this->view_default = $this->cfg[$cfg_idx];
                    break;

            }
            
        }
    }
    

    private function parseRequestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parse_uri = parse_url($uri);
        
        $path_parts = explode('/', $parse_uri['path']);
        $this->query_string = (isset($parse_uri['query'])) ? $parse_uri['query'] : NULL;  
        
        $count = 0;
        while(count($path_parts)){
            $part = array_shift($path_parts);
            
            if(empty($part)){
                continue;
            }
        
            switch ($count) {
                case 0:
                    $this->controller = $this->fixControllerName($part);
                    break;
                
                case 1:
                    $this->action = $this->fixActionName($part);
                    break;

                default:
                    $this->url_params[] = $part;
                    break;
            }
            $count++;
        }        
    }
    
    private function parseRequestQuery()
    {
        if(empty($this->query_string)){
            return;
        }
        
        $get_data = null;
        parse_str($this->query_string, $get_data);
        
        if(count($get_data)){
            $this->get_params = $get_data;
        }
    }
    
    private function fixControllerName($controller)
    {
        $controller = strtolower($controller);
        $controller = urldecode($controller);
        $controller = str_replace( ['_','-', strtolower(self::CONT_SUFIX)], [' ',' ', ''], $controller);
        $controller = trim($controller);
        $controller = ucwords($controller);
        $controller = str_replace(' ', '', $controller);
        $this->view_path = $controller;
        
        return $controller . self::CONT_SUFIX;
    }
    
    private function fixActionName($action)
    {
        $action = strtolower($action);
        $action = urldecode($action);
        $action = str_replace( ['_','-'], [' ',' '], $action);
        $action = trim($action);
        $action = ucwords($action);
        $action = str_replace(' ', '', $action);
        
        $action[0] = strtolower(
            substr($action, 0, 1)
        );
        
        return $action;
    }
    
    public function run()
    {
        $this->parseClassController();
        $this->parseAction();
        $this->invokeController();       
    }
    
    private function parseClassController()
    {
        $this->controller_class_name = "{$this->controller_namespace}\\{$this->controller}";
        $this->reflectionController = new \ReflectionClass($this->controller_class_name);
        
        $chk = $this->reflectionController->isSubclassOf('Fhc\Controller\Base\AbsController');
        if(!$chk){
            throw new Excp\RuntimeException("Controller({$this->controller_class_name}) require extends AbsController");
        }
        
        $this->controller_obj = $this->reflectionController->newInstance();
    }
    
    private function parseAction()
    {
        $chk = $this->reflectionController->hasMethod($this->action);
        if(!$chk){
            throw new Excp\RuntimeException("Action({$this->controller_class_name}::{$this->action}) not defined");
        }
        
        $this->reflectionAction = $this->reflectionController->getMethod($this->action);
        
        $chk = $this->reflectionAction->isPublic();
        if(!$chk){
            throw new Excp\RuntimeException("Action({$this->controller_class_name}::{$this->action}) Method is not public");
        }
    }
    
    private function invokeController()
    {
        
        if(count($this->url_params)){
            $this->controller_obj->setParamsUrl($this->url_params);
        }
        
        if(count($this->get_params)){
            $this->controller_obj->setParamsGet($this->get_params);
        }
        
        $this->controller_obj->setIvoked(strtolower($this->view_path), strtolower($this->action) );
        
        $template = $this->view_path .DS. strtolower($this->action);
        $view = new $this->view_default($template);
        
        $this->controller_obj->setView($view);
        $this->controller_obj->preDispatch();
        $this->controller_obj->{$this->action}();
        $this->controller_obj->posDispatch();
    }
}