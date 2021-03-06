<?php
namespace App\Model;

use App\Model\Product\State\Wished;
use App\Model\Product\State\ProductStateBehavior;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class WishedProduct
 * @package App\Model
 *
 * @ORM\Entity
 */
class WishedProduct
{
    use ProductStateBehavior;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Product
     * Many WishedProducts have One Product
     * @ORM\ManyToOne(targetEntity="\App\Model\Product", inversedBy="wishedProducts")
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * Many Products have One ShoppingList
     * @ORM\ManyToOne(targetEntity="\App\Model\ShoppingList", inversedBy="products")
     */
    protected $shoppingList;

    public function __construct(Product $product, int $quantity)
    {
        $this->state    = new Wished;
        $this->product  = $product;
        $this->quantity = $quantity;
    }

    /**
     * @param ShoppingList $shoppingList
     */
    public function setShoppingList(ShoppingList $shoppingList):void {
        $this->shoppingList = $shoppingList;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product {
        return $this->product;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->getProduct()->getName();
    }

    /**
     * @return string
     */
    public function getBrand(): string {
        return $this->getProduct()->getBrand();
    }

    public function getImage(): string {
        return $this->getProduct()->getImage();
    }

}