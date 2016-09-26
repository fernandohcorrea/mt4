<?php

namespace Fhc\Exception;

/**
 * RuntimeException usada para erros em tempo de Execução
 *
 * @author fcorrea
 */
class RuntimeException extends \RuntimeException
{

    public function __construct($message, $code = 500, $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }

}
