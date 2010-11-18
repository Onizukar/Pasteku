<?php
/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 **/
class ApplicationController extends Controller {
    
    public function initialize(){
        $this->render($this->action_name,'pasteku');
        ini_set('display_errors', 'On');
        //error_reporting(E_ALL);
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    }
}
