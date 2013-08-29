<?php
namespace Zf2SimpleAcl\Authentication\Recognizer;

use Zend\Authentication\AuthenticationService;
use Zf2SimpleAcl\Authentication\AuthenticationServiceInterface;
use Zf2SimpleAcl\Entity\User;
use Zf2SimpleAcl\Entity\UserInterface;
use Zf2SimpleAcl\Items\RoleItem;
use Zf2SimpleAcl\Options\RoleOptionsInterface;

class CronRecognizerService implements AuthenticationServiceInterface
{
    /**
     * @var RoleItem
     */
    protected $role = null;

    /**
     * @param AuthenticationService $authService
     */
    public function __construct(RoleOptionsInterface $options)
    {
        foreach ($options->getRoles() as $role) {
            if ($role->getType() == 'cron') {
                $this->role = $role;
            }
        }

        if (is_null($this->role)) {
            throw new Exception\RuntimeException("Cron recognizer enabled but role does not defined,
                                                   you must define role with type 'cron'");
        }
    }

    /**
     * @return boolean
     */
    public function hasIdentity()
    {
        if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Wget/') !== false) {
                return true;
            } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Lynx/') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return UserInterface
     */
    public function getIdentity()
    {
        if (!$this->hasIdentity()) {
            return null;
        }

        $user = new User();
        $user->setRole($this->role->getId());
        return $user;
    }
}