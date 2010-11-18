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
class Cuenta extends ActiveRecord {
    
    public function initialize(){
        $this->validates_uniqueness_of('username');
        $this->validates_uniqueness_of('twitter');
        //$this->validates_uniqueness_of('email');
        $this->validates_email_in('email');
    }

    public function getId($username = null) {
        $username = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        $registro = $this->find_first("conditions: username = '$username'","columns: id");
        return $registro->id;
    }
    
    /*
    * true si se creo correctamente, false si hubo un error
    */
    public function crearUserTW($tid = null) {
        $Cuenta = new Cuenta();
        $Cuenta->rol="usuario";
        $Cuenta->twitter = $tid;
        if($Cuenta->create()) {
            return true;
        }
        return false;
    }

    /*
    * Funcion que verifica email, devuelve false si es incorrecto
    */
    public function validarEmail($email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            /*** si la validacion falla ***/
            return false;
        }else {
            /*** si es correcto ***/
            return true;
        }
    }
    
    public function getAvatar(){
        if(Session::isset_data('avatar')){
            $img = Session::get('avatar');
            return "<img src='$img'>";
        }
        $ruta = PUBLIC_PATH.'/img/nn.jpg';
        return "<img src='$ruta'>";
    }
    
    public function redirect_tw(){
		Load::lib('TwitterOAuth');
        $keys = Config::read('tw');
        /* Create TwitterOAuth object and get request token */
        $connection = new TwitterOAuth($keys['key']['consumerKey'], $keys['key']['consumerSecret']);
        /* Get request token */
        $request_token = $connection->getRequestToken($keys['key']['callBack']);
        /* Save request token to session */
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        /* If last connection fails don't display authorization link */
        switch ($connection->http_code) {
            case 200:
            /* Build authorize URL */
                $url = $connection->getAuthorizeURL($token);
                header('Location: ' . $url);
                break;
            default:
                echo 'No se ha podido conectar con twitter. Recarge la pagina o intente nuevamente.';
        }
        die();
	}
	
	public function callback_tw(){
		
	}

}
?>
