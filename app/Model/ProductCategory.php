<?php namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProductCategory
 * @package Tests\Unit
 *
 * @ORM\Entity
 * @ORM\Table(name="product_categories")
 */
class ProductCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $slug;
    /**
     * One Category has Many Offers
     * @var Collection|Offers[]
     * @ORM\OneToMany(targetEntity="\App\Model\Offer", mappedBy="category", cascade={"persist"})
     */
    protected $offers;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function __toString()
    {
        return self::class . "({$this->id},{$this->slug})";
    }
}