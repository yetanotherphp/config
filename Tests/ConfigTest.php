<?php

namespace YetAnother\Config\Tests;

use YetAnother\Config\Config;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet_Simple()
    {
        $config = new Config(__DIR__.'/config/config.php');

        $parameter = $config->get('parameter');
        $database = $config->get('database');
        $routing = $config->get('routing');
        $security = $config->get('security');
        $notExistParameter = $config->get('not_exist_parameter');
        $this->assertEquals($parameter, 'value');
        $this->assertEquals($database['host'], 'localhost');
        $this->assertEquals($routing['namespace'], 'My\\Namespace');
        $this->assertEquals($security['roles'], array('ROLE_USER', 'ROLE_ADMIN'));
        $this->assertEquals($notExistParameter, null);
    }

    public function testGet_Directory()
    {
        $config = new Config(__DIR__.'/config');

        $database = $config->get('database');
        $routing = $config->get('routing');
        $security = $config->get('security');
        $this->assertEquals($database['user'], 'root');
        $this->assertEquals($routing['routers'], array(
            '/admin' => 'AdminController:dashboard',
            '/' => 'SiteController:homepage'
        ));
        $this->assertEquals($security['access_control'], array(
            '^/admin' => 'ROLE_ADMIN'
        ));
    }

    public function testGet_Array()
    {
        $myConfig = array(
            'my_parameter1' => 'value1',
            'my_parameter2' => 'value2',
        );
        $config = new Config($myConfig);
        $this->assertEquals($config->get('my_parameter1'), 'value1');
    }

    /**
     * @expectedException \YetAnother\Config\ConfigException
     */
    public function testGet_NotExistFile()
    {
        $config = new Config(__DIR__.'/config');
        $config->get('not_exist_file');
    }

    public function testSet()
    {
        $config = new Config(__DIR__.'/config/config.php');
        $config->set('my_parameter', 'my_value');
        $this->assertEquals($config->get('my_parameter'), 'my_value');
    }

    public function testHas()
    {
        $config = new Config(__DIR__.'/config/config.php');

        $this->assertFalse($config->has('my_parameter'));
        $config->set('my_parameter', 'my_value');
        $this->assertTrue($config->has('my_parameter'));
    }

    public function testRemove_Exist()
    {
        $config = new Config(__DIR__.'/config/config.php');

        $config->remove('database');
        $this->assertFalse($config->has('database'));
    }

    public function testRemove_NotExist()
    {
        $config = new Config(__DIR__.'/config/config.php');

        $config->remove('not_exist_parameter');
        $this->assertFalse($config->has('not_exist_parameter'));
    }

    public function testAll()
    {
        $config = new Config(__DIR__.'/config');

        $all = $config->all();
        $this->assertEquals(array_keys($all), array('config', 'database', 'routing', 'security'));
    }

    /********** ArrayAccess functions **********/

    public function testOffsetGet()
    {
        $config = new Config(__DIR__.'/config/config.php');

        $database = $config['database'];
        $this->assertEquals($database['host'], 'localhost');
    }

    public function testOffsetSet_NotNullOffset()
    {
        $config = new Config(__DIR__.'/config/config.php');
        $config['my_parameter'] = 'my_value';
        $this->assertEquals($config['my_parameter'], 'my_value');
    }

    public function testOffsetExist_IfExist()
    {
        $config = new Config(__DIR__.'/config/config.php');
        $this->assertTrue(isset($config['database']));
    }

    public function testOffsetExist_IfNotExist()
    {
        $config = new Config(__DIR__.'/config/config.php');
        $this->assertFalse(isset($config['not_exist_parameter']));
    }

    public function testOffsetUnset_Exist()
    {
        $config = new Config(__DIR__.'/config/config.php');

        unset($config['database']);
        $this->assertFalse(isset($config['database']));
    }

    public function testOffsetUnset_NotExist()
    {
        $config = new Config(__DIR__.'/config/config.php');

        unset($config['not_exist_parameter']);
        $this->assertFalse(isset($config['not_exist_parameter']));
    }

}