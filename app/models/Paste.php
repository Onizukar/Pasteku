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
class Paste extends ActiveRecord {

    protected $before_create  = 'pre_create';
    
    public function get_hijos($padre = null){
        $id = filter_var($padre, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            return $this->find("conditions: padre = '$id'",'columns: id, titulo, creado_at ');
        }
        return false;
    }
    
    public function pre_create(){
        if(Auth::is_valid()){
            $this->cuenta_id = Auth::get('id');
        }
    }
    
    public function getPost(){
        if(Auth::is_valid()){
            $id = Auth::get('id');
            return $this->find("conditions: cuenta_id = '$id'",'columns: id, titulo, syntax, visitas, expiracion, creado_at ');
        }
    }
    
    public function borrar($id = null){
        if(Auth::is_valid()){
            if($this->cuenta_id = Auth::get('id')){
                $posti = $this->id;
                $this->update_all("padre = 'null'","padre = $posti");
                $this->delete();
            }
        }
    }
}
?>
