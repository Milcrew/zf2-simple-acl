<?php
namespace Zf2SimpleAcl\Options;

use Zend\Stdlib\AbstractOptions;
class ModuleOptions extends AbstractOptions
    implements RestrictionOptionsInterface
{
    /**
     * @var array
     */
    protected $restrictions = array();

    /**
     * @return array
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param array $restrictions
     * @return ModuleOptions
     */
    public function setRestrictions(array $restrictions)
    {
        $this->restrictions = $restrictions;
        return $this;
    }
}
