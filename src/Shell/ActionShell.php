<?php

namespace HavokInspiration\ActionsClass\Shell;

use Cake\Console\Shell;
use Cake\Core\ConventionsTrait;
use Cake\Filesystem\File;
use Cake\Utility\Text;

/**
 * Fixtures shell command.
 */
class ActionShell extends Shell
{
    use ConventionsTrait;

    /**
     * @var string
     */
    protected $templateFile = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->addOptions([
                'controller' => [
                    'short'     => 'c',
                    'help'      => 'The controller folder'
                ],
                'action' => [
                    'short'     => 'a',
                    'help'      => 'The action file'
                ],
                'prefix' => [
                    'help'      => 'The prefix name'
                ],
                'plugin' => [
                    'help'      => 'The plugin folder'
                ]              
            ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool Success or not.
     */
    public function main()
    {
        if (!$this->param('controller')) {
            $this->out('<error>The controller name (-c Controller) is needed</error>');

            return false;
        }

        // The action Template available on HavokInspiration/cakephp-actions-class plugin
        $this->templateFile = dirname(__DIR__) . DS . 'Template' . DS . 'Bake' . DS;

        // Controller name use CakePHP Convention
        $controllerName = $this->_inflected($this->param('controller'));
        if (empty($controllerName)) {
            $this->out('<error>The controller name is empty.</error>');
            $this->out('<info>Respect the CakePHP convention.</info>');

            return false;
        }
        $this->data['{{controller}}'] = $controllerName;
        $this->data['{{controller.lower}}'] = strtolower($controllerName);

        $directory = APP;
        $nameSpace = 'App';

        // Go to the plugin directory
        if ($this->param('plugin')) {
            $pluginName = $this->_inflected($this->param('plugin'));
            if (!empty($pluginName)) {
                $nameSpace = $pluginName;
                $directory = ROOT . DS . 'plugins' . DS . $pluginName . DS . 'src' . DS;
            }
        }

        // It's always in Controller directory
        $directory .= 'Controller' . DS;

        // Add prefix name and directory
        if ($this->param('prefix')) {
            $prefixname = $this->_inflected($this->param('prefix'));
            if (!empty($prefixname)) {
                $nameSpace .= '\\' . $prefixname;
                $directory .= $prefixname . DS;
            }
        }

        // Index is the default action if -a is not define
        if ($this->param('action')) {
            $actionName = $this->_inflected($this->param('action'));
        }
        if (empty($actionName)) {
            $actionName = 'Index';
        }
        $this->data['{{name}}'] = $actionName;

        $this->data['{{namespace}}'] = $nameSpace;

        $this->_createActionFile($directory);
        $this->_createTestFile();

        return true;
    }

    /**
     * Create Controller Action file
     *
     * @param string $directory The directory to controller
     */
    protected function _createActionFile($directory)
    {
        $file = new File($this->templateFile . 'Action' . DS . 'action.ctp');
        // Assign data
        $contents = str_replace(array_keys($this->data), array_values($this->data), $file->read());

        $this->createFile($directory . $this->data['{{controller}}'] . DS . $this->data['{{name}}'] . 'Action.php', $contents);
    }

    /**
     * Create TestCase file
     */
    protected function _createTestFile()
    {
        $file = new File($this->templateFile . 'Tests' . DS . 'action.ctp');
        // Assign data
        $contents = str_replace(array_keys($this->data), array_values($this->data), $file->read());

        $this->createFile(TESTS . 'TestCase' . DS . $this->data['{{controller}}'] . DS . $this->data['{{name}}'] . 'Action.php', $contents);
    }

    /**
     * Correct string with Text::slug and use CakePHP convention
     *
     * @param string $string
     * @see https://book.cakephp.org/3.0/en/core-libraries/text.html#Cake\Utility\Text::slug
     * @see https://book.cakephp.org/3.0/en/intro/conventions.html
     *
     * @return string The string after Text::slug and 
     */
    protected function _inflected($string)
    {
        return $this->_camelize(Text::slug($string, ['replacement' => '_']));
    }
}
