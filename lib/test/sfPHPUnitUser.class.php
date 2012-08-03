<?php
/**
 * Created by JetBrains PhpStorm.
 * User: msmith
 * Date: 8/2/12
 * Time: 8:30 PM
 * To change this template use File | Settings | File Templates.
 */
class sfPHPUnitUser extends myUser
{
    private $_credentials = array();

    private $_isAuthenticated = 0;

    public function hasCredential($credential, $useAnd = true)
    {
        file_put_contents('/var/www/synoffice/error.txt', var_export($credential, true));

        $this->logCredential($credential);

        return parent::hasCredential($credential, $useAnd);
    }

    private function logCredential($credential)
    {
        if(is_array($credential)){
            foreach($credential as $permission){
                $this->logCredential($permission);
            }
        }elseif(null !== $credential){
            isset($this->_credentials[$credential]) ? $this->_credentials[$credential]++ : $this->_credentials[$credential] = 1;
        }
    }

    public function isAuthenticated()
    {
        $this->_isAuthenticated++;

        return parent::isAuthenticated();
    }

    public function getCalledCredentials()
    {
        return $this->_credentials;
    }

    public function wasCredentialCalled($credential)
    {
        return isset($this->_credentials[$credential]);
    }

    public function getCalledIsAuthenticated()
    {
        return $this->_isAuthenticated;
    }

    public function wasIsAuthenticatedCalled()
    {
        return $this->_isAuthenticated > 0;
    }
}
