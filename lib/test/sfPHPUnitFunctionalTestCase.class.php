<?php
/**
 * Created by JetBrains PhpStorm.
 * User: msmith
 * Date: 8/2/12
 * Time: 8:18 PM
 * To change this template use File | Settings | File Templates.
 */
class sfPHPUnitFunctionalTestCase extends sfPHPUnitBaseFunctionalTestCase
{
    protected static $first = true;

    protected function _start()
    {
        parent::_start();

        $lastTime = 0;
        if(file_exists($log_file = sfConfig::get('sf_log_dir') . '/last_phpunit_time')){
            $lastTime = file_get_contents($log_file);
        }

        if(self::$first){
            if(time() > $lastTime + 900){
                $this->reloadDB();
            }
            file_put_contents($log_file, time());
            self::$first = false;
        }

        Doctrine_Manager::getInstance()->getCurrentConnection()->beginTransaction();
    }

    protected function _end()
    {
        parent::_end();

        Doctrine_Manager::getInstance()->getCurrentConnection()->rollback();
    }

    protected function reloadDB()
    {
        new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));

        Doctrine_Core::loadModels(sfConfig::get('sf_lib_dir').'/model/doctrine');

        Doctrine_Core::dropDatabases();
        Doctrine_Core::createDatabases();
        Doctrine_Core::createTablesFromModels(sfConfig::get('sf_lib_dir') . '/model');
        Doctrine_Core::loadData(array(sfConfig::get('sf_data_dir') . '/fixtures', sfConfig::get('sf_test_dir') . '/fixtures/functional'));

        SalesEntityTable::getInstance()->createQuery('s')->update()->set('use_address_validation', '?', '0')->execute();

        Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);;
//        Doctrine_Manager::resetInstance();
    }

    protected function signIn($username = 'admin', $password = 'synlawn4me')
    {
        $this->getBrowser()
            ->get('/login')
            ->click('sign in', array('signin' => array('username' => $username, 'password' => $password)), array('_with_csrf' => true))
            ->with('user')->isAuthenticated();

    }
}
