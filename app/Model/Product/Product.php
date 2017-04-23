<?php
namespace App\Model\Product;

use App\Model\Product\Price;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $brand;

    /**
     * @var Price
     */
    private $price;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $image;

    public function __construct(string $name, string $brand,
                                Price $price, string $image = '')
    {
        $this->name = $name;
        $this->brand = $brand;
        $this->price = $price;
        $this->image = $image;
    }

    /*
     * Getters
     */

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    /*
     * Comparing
     */

    public function equals(Product $another): bool
    {
        return $this->getName() == $another->getName()
            && $this->getBrand() == $another->getBrand();
    }
}