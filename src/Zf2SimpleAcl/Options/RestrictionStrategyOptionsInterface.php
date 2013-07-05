<?php
namespace Zf2SimpleAcl\Options;

interface RestrictionStrategyOptionsInterface
{
    const PERMISSIVE_STRATEGY = 'permissive';
    const STRICT_STRATEGY = 'strict';

    /**
     * @return boolean
     */
    public function isPermissive();

    /**
     * @return boolean
     */
    public function isStrict();

    /**
     * @param string $strategy
     * @return RestrictionStrategyOptionsInterface
     */
    public function setRestrictionStrategy($strategy);
}
