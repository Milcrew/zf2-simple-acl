<?php
namespace Zf2SimpleAcl\Items;

class RoleItem
{
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
