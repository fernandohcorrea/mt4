<?php

namespace Fhc\Controller\Base;

/**
 * Description of AbsController
 *
 * @author fcorrea
 */
abstract class AbsController
{

    /**
     * @var \Fhc\View\Base\AbsView
     */
    protected $view = null;

    /**
     * Parametro de Path da URL
     * @var Array | null
     */
    protected $url_params = null;

    /**
     * Parametros de Query_string(GET) da URL
     * 
     * @var Array | null
     */
    protected $get_params = null;

    /**
     * Parâmetros de POST 
     * @var Array | null
     */
    protected $post_params = null;

    /**
     * Parâmetros de $_FILES
     * @var Array | null
     */
    protected $files_params = null;

    /**
     * Nome do Controller Chamado
     * @var String
     */
    public $controller_name;

    /**
     * Nome da Action Chamada
     * @var String
     */
    public $action_name;

    /**
     * Obter Parametros de PATH da URL
     * @return Array | Null
     */
    public function getParamsUrl()
    {
        return $this->url_params;
    }

    /**
     * Obter Parâmetros de Get
     * 
     * @todo Passar o post por um filtro
     * @return mixed
     */
    public function getParamsGet()
    {
        if (is_null($this->get_params)) {
            $this->get_params = $_GET;
        }
        return $this->get_params;
    }

    /**
     * Obter Parâmetros de Post
     * 
     * @todo Passar o post por um filtro
     * @return mixed
     */
    public function getParamsPost()
    {
        if (is_null($this->post_params)) {
            $this->post_params = $_POST;
        }
        return $this->post_params;
    }
    
    /**
     * Obter Parâmetros de Post
     * 
     * @todo Passar o post por um filtro
     * @return mixed
     */
    public function getParamsFiles()
    {
        if (is_null($this->files_params)) {
            $this->files_params = $_FILES;
        }
        return $this->files_params;
    }

    /**
     * Define Parâmatros de PATH da URL
     * 
     * @param Array $url_params
     * @return \Fhc\Controller\Base\AbsController
     */
    public function setParamsUrl(Array $url_params)
    {
        $this->url_params = $url_params;
        return $this;
    }

    /**
     * Define Parâmatros de GET da URL
     * 
     * @param Array $get_params
     * @return \Fhc\Controller\Base\AbsController
     */
    public function setParamsGet(Array $get_params)
    {
        $this->get_params = $get_params;
        return $this;
    }

    /**
     * Define Parâmatros de POST
     * @param Array $post_params
     * @return \Fhc\Controller\Base\AbsController
     */
    public function setParamsPost(Array $post_params)
    {
        $this->post_params = $post_params;
        return $this;
    }

    public function setIvoked($controller_name, $action_name)
    {
        $this->controller_name = $controller_name;
        $this->action_name = $action_name;
    }

    /**
     * Obter View
     * @return \Fhc\View\Base\AbsView
     */
    function getView()
    {
        return $this->view;
    }

    /**
     * Define View
     * @param \Fhc\View\Base\AbsView $view
     */
    public function setView(\Fhc\View\Base\AbsView $view)
    {
        $this->view = $view;
    }
    
    /**
     * Checa o requeste é ajax
     * 
     * @return boolean
     */
    protected function isAjaxRequest()
    {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    /**
     * Pre Dispatch chamado antes da ACTION
     */
    public function preDispatch()
    {
        
    }

    /**
     * Pos Dispatch chamado depois da ACTION
     */
    public function posDispatch()
    {
        
    }

}
