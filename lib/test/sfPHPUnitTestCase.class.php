<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfBasePHPUnitTestCase is the super class for all unit
 * tests using PHPUnit.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage test
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPHPUnitTestCase extends sfPHPUnitBaseTestCase
{
    protected static $first = true;

    protected function setupDB(){
        $this->db = new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
    }

    protected function loadDB($force = false){
        if(! $this->db){
            $this->setupDB();
        }

        $lastTime = 0;
        if(file_exists($log_file = sfConfig::get('sf_log_dir') . '/last_phpunit_time')){
            $lastTime = file_get_contents($log_file);
        }

        if((self::$first && time() > $lastTime + 900) || $force){
            Doctrine_Core::loadModels(sfConfig::get('sf_lib_dir').'/model/doctrine');

            Doctrine_Core::dropDatabases();
            Doctrine_Core::createDatabases();
            Doctrine_Core::createTablesFromModels(sfConfig::get('sf_lib_dir') . '/model');
            Doctrine_Core::loadData(array(sfConfig::get('sf_data_dir') . '/fixtures', sfConfig::get('sf_test_dir') . '/fixtures/functional'));

            SalesEntityTable::getInstance()->createQuery('s')->update()->set('use_address_validation', '?', '0')->execute();

            Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);;
        }

        file_put_contents($log_file, time());
        self::$first = false;

        try{
            Doctrine_Manager::getInstance()->getCurrentConnection()->rollback();
        }catch (Exception $e){

        }
        Doctrine_Manager::getInstance()->getCurrentConnection()->beginTransaction();
    }

}
