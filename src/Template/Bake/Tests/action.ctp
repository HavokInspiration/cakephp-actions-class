<?php
/**
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) HavokInspiration/cakephp-actions-class
 * @link          http://github.com/HavokInspiration/cakephp-actions-class
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * {{controller}} Controller
 * {{name}} Action
 *
 * @package {{namespace}}\Controller
 */
class {{name}}ActionTest extends IntegrationTestCase
{
	/**
	 * TestCase for \{{namespace}}\Controller\{{controller}}\{{name}}Action
	 */
    public function test{{name}}Action()
    {
        $this->get('/{{controller.lower}}');
        $this->assertResponseOk();
    }
}
