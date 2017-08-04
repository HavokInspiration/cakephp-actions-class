<?php

namespace HavokInspiration\ActionsClass\Shell;

use Cake\Console\Shell;
use Cake\Core\ConventionsTrait;
use Cake\Database\Exception;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Utility\Inflector;

/**
 * Fixtures shell command.
 */
class ActionShell extends Shell
{
    use ConventionsTrait;

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
     * @return bool|int Success or error code.
     */
    public function main()
    {
        if (!$this->param('controller')) {
            $this->out('<error>The controller name (-c Controller) is needed</error>');

            return false;
        }

        $directory = APP;
        $data['{{namespace}}'] = 'App';

        // Go to the plugin directory
        if ($this->param('plugin')) {
            $data['{{namespace}}'] = $this->_camelize($this->param('plugin'));
            $directory = ROOT . DS . 'plugins' . DS . $data['{{namespace}}'] . 'src' . DS;
        }

        // It's always in Controller directory
        $directory .=  'Controller' . DS;

        // Add prefix name and directory
        if ($this->param('prefix')) {
            $prefixname .= '\\' . $this->_camelize($this->param('prefix'));
            $data['{{namespace}}'] = $prefixname;
            $directory .= $prefixname . DS;
        }

        // Controller name use CakePHP Convention
        $controllerName = $this->_camelize($this->param('controller'));
        $data['{{controller}}'] = $controllerName;
        
        // Find and create folder
        $folder = new Folder($directory . $controllerName, true);

        // Index is the default action if -a is not define
        $data['{{name}}'] = 'Index';
        if ($this->param('action')) {
            $actionName = $this->_camelize($this->param('action'));
            $data['{{name}}'] = $actionName;
        }

        // The action Template available on HavokInspiration/cakephp-actions-class plugin
        $templateFile = dirname(__DIR__) . DS . 'Template' . DS . 'Bake' . DS . 'Action' . DS . 'action.ctp';
        $file = new File($templateFile);
        // Assign data
        $contents = str_replace(array_keys($data), array_values($data), $file->read());

        $this->createFile($folder->pwd() . DS . $actionName . 'Action.php', $contents);
    }
}
