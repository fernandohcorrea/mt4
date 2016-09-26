<?php

namespace Fhc\Exception;

/**
 * LogicException usada para errors de Lógica
 *
 * @author fcorrea
 */
class LogicException extends \LogicException
{

    public function __construct($message, $code = 500, $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

}
