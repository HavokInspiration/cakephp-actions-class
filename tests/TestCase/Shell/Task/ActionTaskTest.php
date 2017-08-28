<?php
/**
 * Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Yves Piquel (http://www.havokinspiration.fr)
 * @link          http://github.com/HavokInspiration/cakephp-actions-class
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace HavokInspiration\ActionsClass\Test\TestCase\Shell\Task;

use Bake\Shell\Task\BakeTemplateTask;
use Cake\Core\Plugin;
use Cake\TestSuite\StringCompareTrait;
use Cake\TestSuite\TestCase;

class ActionTaskTest extends TestCase
{

    use StringCompareTrait;

    public $Task;

    /**
     * setup method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_compareBasePath = Plugin::path('HavokInspiration/ActionsClass') . 'tests' . DS . 'comparisons' . DS . 'Controller' . DS;

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')
            ->disableOriginalConstructor()
            ->getMock();

        $this->Task = $this->getMockBuilder('HavokInspiration\ActionsClass\Shell\Task\ActionTask')
            ->setMethods(['in', 'err', 'createFile', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->Task->Test = $this->getMockBuilder('Bake\Shell\Task\TestTask')
            ->setMethods(['in', 'err', 'createFile', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->Task->BakeTemplate = new BakeTemplateTask($io);
        $this->Task->BakeTemplate->initialize();
        $this->Task->BakeTemplate->interactive = false;
        $this->Task->Test->BakeTemplate = new BakeTemplateTask($io);
        $this->Task->Test->BakeTemplate->initialize();
        $this->Task->Test->BakeTemplate->interactive = false;
    }

    /**
     * Load a plugin from the tests folder, and add to the autoloader
     *
     * @param string $name plugin name to load
     * @return void
     */
    protected function _loadTestPlugin($name)
    {
        $path = TESTS . 'test_app' . DS . 'Plugin' . DS . $name . DS;

        Plugin::load($name, [
            'path' => $path,
            'autoload' => true
        ]);
    }

    /**
     * Test the main method.
     *
     * @return void
     */
    public function testMain()
    {
        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath(APP . 'Controller/Posts/IndexAction.php'),
                $this->stringContains('class IndexAction extends Action')
            );

        $this->Task->expects($this->at(1))
            ->method('createFile')
            ->with(
                $this->_normalizePath(TESTS . 'TestCase/Controller/Posts/IndexActionTest.php'),
                $this->stringContains('class IndexActionTest extends IntegrationTestCase')
            );

        $this->Task->main('Posts/Index');
    }

    /**
     * Test the main method with the no-test parameter will only call createFile once.
     *
     * @return void
     */
    public function testMainNoTest()
    {
        $this->Task->expects($this->once())
            ->method('createFile');

        $this->Task->params['no-test'] = true;
        $this->Task->main('Posts/Index');
    }

    /**
     * Test the main method with a plugin.
     *
     * @return void
     */
    public function testMainPlugin()
    {
        $this->_loadTestPlugin('MaintenanceTest');
        $path = Plugin::path('MaintenanceTest');

        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath($path . 'src/Controller/Posts/IndexAction.php'),
                $this->stringContains('class IndexAction extends Action')
            );

        $this->Task->expects($this->at(1))
            ->method('createFile')
            ->with(
                $this->_normalizePath($path . 'tests/TestCase/Controller/Posts/IndexActionTest.php'),
                $this->stringContains('class IndexActionTest extends IntegrationTestCase')
            );

        $this->Task->main('MaintenanceTest.Posts/Index');
    }

    /**
     * Test bake.
     *
     * @return void
     */
    public function testBake()
    {
        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath(APP . 'Controller/Posts/IndexAction.php')
            );

        $result = $this->Task->bake('Posts/Index');
        $this->assertSameAsFile('Posts/IndexAction.php', $result);
    }

    /**
     * Test bake with a plugin.
     *
     * @return void
     */
    public function testBakePlugin()
    {
        $this->_loadTestPlugin('MaintenanceTest');
        $path = Plugin::path('MaintenanceTest');

        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath($path . 'src/Controller/Posts/IndexAction.php')
            );

        $this->Task->plugin = 'MaintenanceTest';
        $result = $this->Task->bake('Posts/Index');
        $this->assertSameAsFile('Plugin/Posts/IndexAction.php', $result);
    }

    /**
     * Test bake with a routing prefix.
     *
     * @return void
     */
    public function testBakePrefix()
    {
        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath(APP . 'Controller/Admin/Posts/IndexAction.php')
            );

        $this->Task->params['prefix'] = 'Admin';
        $result = $this->Task->bake('Posts/Index');
        $this->assertSameAsFile('Admin/Posts/IndexAction.php', $result);
    }

    /**
     * Test bake with a plugin and a routing prefix.
     *
     * @return void
     */
    public function testBakePluginPrefix()
    {
        $this->_loadTestPlugin('MaintenanceTest');
        $path = Plugin::path('MaintenanceTest');

        $this->Task->expects($this->at(0))
            ->method('createFile')
            ->with(
                $this->_normalizePath($path . 'src/Controller/Admin/Posts/IndexAction.php')
            );

        $this->Task->params['prefix'] = 'Admin';
        $this->Task->plugin = 'MaintenanceTest';
        $result = $this->Task->bake('Posts/Index');
        $this->assertSameAsFile('Plugin/Admin/Posts/IndexAction.php', $result);
    }
}
