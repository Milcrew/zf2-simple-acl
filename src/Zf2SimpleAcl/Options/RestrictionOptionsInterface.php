<?php
namespace Zf2SimpleAcl\Options;

interface RestrictionOptionsInterface
{
    /**
     * @return array
     */
    public function getRestrictions();

    /**
     * @param array $restrictions
     * @return RestrictionOptionsInterface
     */
    public function setRestrictions(array $restrictions);
}
