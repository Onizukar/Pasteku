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

class AccesoController extends ApplicationController {

    public function index() {
        if(Auth::is_valid()){
            $this->avatar = $this->Cuenta->getAvatar();
        }
        else{
            if($this->has_request('password') and $this->has_request('username')){
                $pwd = md5($this->request('password'));
                $username = $this->request('username');
                $auth = new Auth("model", "class: Cuenta", "username: $username","password: $pwd");
                if ($auth->authenticate()) {
                    Flash::success("Usuario identificado");
                    $this->avatar = $this->Cuenta->getAvatar();
                    $this->redirect('panel');
                }
                else {
                    Flash::error("No pudo iniciar sesion, verifique los datos de acceso");
                }
            }
        }
    }

    public function _redirect() {
        $this->Cuenta->redirect_tw();
    }

    public function _callbackTW() {
        Load::lib('TwitterOAuth');
        $keys = Config::read('tw');
        //session_start();
        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
            $_SESSION['oauth_status'] = 'oldtoken';
            $this->redirect("acceso/loginTW");
        }
        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($keys['key']['consumerKey'],$keys['key']['consumerSecret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;
        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            $_SESSION['status'] = 'verified';
            /* Get credentials to test API access */
            $credentials = $connection->get('account/verify_credentials');
            if (isset($credentials->error)) {
                $this->msg = $credentials->error."<br><br><a href='http://camilotilaguy.com/pasteku/acceso/loginTW'>Registrese ahora</a>";
            }
            else {
                //El usuario se logeo con TW
                $twitter = $credentials->id;
                $auth = new Auth("model", "class: Cuenta", "twitter: $twitter");
                if ($auth->authenticate()) {
                    Session::set('avatar',$credentials->profile_image_url);
                    $this->redirect("panel");
                }
                else {
                    
                    if(!$this->Cuenta->crearUserTW($twitter)) {
                        Flash::error('No se pudo cumplir la petición, intentelo de nuevo');
                        Flash::error("No se guardo pudo registrar");
                    }
                    else{
                        $auth = new Auth("model", "class: Cuenta", "twitter: $twitter");
                        $this->redirect("panel");
                    }
                }
            }
            die();
        }
    }

    /*
    * Se registra al usuario
    */
    public function registro() {
        if ($this->has_post('cuenta')) {
            $nuevaCuenta = new Cuenta($this->post('cuenta'));
            $nuevaCuenta->password = md5($nuevaCuenta->password);
            if($nuevaCuenta->create()) {
                //registro exitoso
                $pwd = $nuevaCuenta->password;
                $username = $nuevaCuenta->username;
                $auth = new Auth("model", "class: Cuenta", "username: $username","password: $pwd");
                if ($auth->authenticate()) {
                    Flash::success("Usuario identificado");
                    //$this->redirect("acceso/index");
                    $this->redirect("panel");
                }
            }
        }
    }

    /*
    * Logout general, sirve tanto para facebook, twitter y para Auth clasico
    */
    public function logout() {
        Auth::destroy_identity();
        session_destroy();
        Flash::success("Cerro sesion");
        $this->route_to("controller: paste","action: index");
    }
}
?>
