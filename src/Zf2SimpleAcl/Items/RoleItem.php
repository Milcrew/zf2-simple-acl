<?php
namespace Zf2SimpleAcl\Items;

class RoleItem
{
    const TYPE_GENERIC = 'generic';

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!array_key_exists('id', $data)) {
            throw new \InvalidArgumentException("Identifier for RoleOption is required");
        }

        if (!array_key_exists('name', $data)) {
            throw new \InvalidArgumentException("Name for RoleOption is required");
        }

        if (!array_key_exists('data', $data) || !array_key_exists('type', $data['data'])) {
            $data['data']['type'] = self::TYPE_GENERIC;
        }

        $this->data = $data;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data['data'];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @return null
     */
    public function getParent()
    {
        if (!array_key_exists('parent', $this->data)) {
            return null;
        }
        return $this->data['parent'];
    }

}
