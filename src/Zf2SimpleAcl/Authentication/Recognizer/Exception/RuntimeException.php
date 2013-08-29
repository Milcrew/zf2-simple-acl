<?php
namespace Zf2SimpleAcl\Authentication\Recognizer\Exception;

use Zf2SimpleAcl\Exception\ExceptionInterface;

class RuntimeException extends \InvalidArgumentException implements ExceptionInterface {}