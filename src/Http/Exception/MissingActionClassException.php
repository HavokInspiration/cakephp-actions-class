<?php
namespace HavokInspiration\ActionsClass\Http\Exception;

use Cake\Core\Exception\Exception;

class MissingActionClassException  extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Action class %s could not be found.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code);
    }
}
