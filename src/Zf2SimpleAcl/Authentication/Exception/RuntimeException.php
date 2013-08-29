<?php
namespace Zf2SimpleAcl\Authentication\Exception;

use Zf2SimpleAcl\Exception\ExceptionInterface;

class RuntimeException extends \InvalidArgumentException implements ExceptionInterface {}