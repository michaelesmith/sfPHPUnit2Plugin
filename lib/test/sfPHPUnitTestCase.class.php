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
    protected function setupDB(){
        $this->db = new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
    }

}
