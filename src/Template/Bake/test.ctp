<?php
namespace <%= $namespace %>\Test\TestCase\Controller<%= $prefix %>\<%= $controller %>;

use Cake\TestSuite\IntegrationTestCase;

/**
 * Controller <%= $controller %>
 * Action <%= $action %>
 *
 * @package <%= $namespace %>\Controller
 */
class <%= $action %>ActionTest extends IntegrationTestCase
{
    /**
     * TestCase for \<%= $namespace %>\Controller\<%= $controller %>\<%= $action %>Action
     */
    public function test<%= $action %>Action()
    {
        $this->get('<%= str_replace('\\', '/', strtolower($prefix)) %>/<%= strtolower($controller) %>/<%= strtolower($action) %>');
        $this->assertResponseOk();
    }
}
