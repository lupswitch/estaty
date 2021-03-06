<?php

namespace Estaty\Model\Property;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @Entity
 */
class PropertyType
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    private $slug;

    /**
     * @OneToMany(targetEntity="PropertyType", mappedBy="parent")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $children;

    /**
     * @ManyToOne(targetEntity="PropertyType", inversedBy="children")
     * @JoinColumn(name="parentId", referencedColumnName="id")
     * @var \Estaty\Model\Property\PropertyType
     */
    private $parent;

    public function __construct($name, $slug, PropertyType $parent = null)
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->name = $name;
        $this->slug = $slug;

        if ($parent) {
            $this->parent = $parent;
            $this->parent->getChildren()->add($this);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields'  => ['slug'],
            'message' => 'PropertyType slugs must be unique!',
        ]));

        $metadata->addPropertyConstraints('name', [
            new Assert\NotBlank(),
        ]);

        $metadata->addPropertyConstraints('slug', [
            new Assert\NotBlank(),
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRoot()
    {
        return $this->parent ?: $this;
    }
}
