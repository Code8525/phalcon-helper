<?php
declare(strict_types=1);

namespace Phalcon;

use Throwable;

/**
 * Class ApiException
 * @package Phalcon
 */
class ApiException extends Exception
{

    private $error;

    /**
     * ApiException constructor.
     * @param string $error
     * @param string $message
     * @param int $code
     * @param Throwable $previous
     */
    public function __construct(string $error, string $message = null, int $code = 0, Throwable $previous = null)
    {
        $this->error = $error;
        if ($message === null)
            $message = '';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

}
