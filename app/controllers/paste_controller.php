<?php
/**
 * Pasteku - KumbiaPHP Pastebin
 * PHP version 5
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to oonizukar@gmail.com so we can send you a copy immediately.
 *
 * @author Camilo Tilaguy <oonizukar@gmail.com>
 */

Load::lib('util');
class PasteController extends ApplicationController {

    public function index($id = null) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $this->existe = $this->Paste->exists("id = $id");
            if($this->existe){
                $this->paste = $this->Paste->find_first($id);
                $this->paste->visitas++;
                $this->paste->update();
                $this->paste->padre = filter_var($this->paste->padre, FILTER_VALIDATE_INT);
                $this->hijos = $this->Paste->get_hijos($this->paste->id);
            }
            else{
                Flash::error('El post que busca ya expiro o nunca existio');
            }
        }
    }

    public function nuevo() {
        if($this->has_post('paste')) {
            $paste = new Paste($this->post('paste'));
            if(!$paste->create()){
                Flash::error("No se guardo el paste");
            }
            else{
                $this->redirect('ver/'.$paste->id);
            }
            $this->paste = $paste;
        }
    }
    
    public function verTxT($id = null) {
        $this->set_response("view");
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $paster = $this->Paste->find_first("conditions: id = '$id'",'columns: post');
            $this->post = str_replace('<','&#60;',$paster->post);
            $this->post = nl2br($this->post);
        }
    }
    
    public function descargar($id = null) {
        $this->set_response("view");
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $paster = $this->Paste->find_first("conditions: id = '$id'",'columns: post, syntax');
            $this->post = $paster->post;
            //$this->post = nl2br($this->post);
            $this->extencion = strtolower($paster->syntax);
            $this->nombre = $id.'.txt';
        }
    }
    
    public function mis_post(){
        if(Auth::is_valid()){
            $this->avatar = $this->Cuenta->getAvatar();
            $this->result = $this->Paste->getPost();
            $this->campos = array('Id' => 'id',
                                  'Titulo' => 'titulo',
                                  'Sintasis' => 'syntax',
                                  'Visitas' => 'visitas',
                                  //'Sera borrado' => 'expiracion',
                                  'Fecha' => 'creado_at');
        }
        else{
            $this->redirect('acceso');
        }
    }
    
    public function borrar($id = null){
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $post = $this->Paste->find_first($id);
            $post->borrar();
        }
        $this->redirect('panel');
    }
}
?>