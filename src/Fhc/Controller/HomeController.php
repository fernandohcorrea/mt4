<?php

namespace Fhc\Controller;


/**
 * Description of HomeController
 *
 * @author fcorrea
 */
class HomeController extends BaseLayoutController
{
    public  function index()
    {
        $this->view->render();
    }

}
