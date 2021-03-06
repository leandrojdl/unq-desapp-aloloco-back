<?php
namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Tests\Builders\UserBuilder;
use App\Model\Box;
use App\Model\Market;
use App\Model\ShoppingList;
use App\Model\WishedProduct;
use App\Model\Threshold\GeneralThreshold;
use App\Model\Threshold\CategoryThreshold;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class UserTest
 * @package Tests\Unit
 */
class UserTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_is_initialized_with_a_market_and_an_email(): void {
        // Arrange
        $email    = 'user@mail.com';
        $username = 'username';
        $address  = 'Calle Falsa 123';
        $googleId = 111222333;
        $market = Mockery::mock(Market::class);
        $jon = UserBuilder::new()
            ->withMarket($market)
            ->withEmail($email)
            ->withUsername($username)
            ->withAddress($address)
            ->withGoogleId($googleId)
            ->build();

        // Assert
        $this->assertEquals($email, $jon->getEmail());
        $this->assertEquals($username, $jon->getUsername());
        $this->assertEquals($address, $jon->getAddress());
        $this->assertEquals($googleId, $jon->getGoogleId());
        $this->assertEquals($market, $jon->getMarket());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_add_a_new_shopping_list(): void {
        // Arrange
        $jon = UserBuilder::anyUserBuiltWithMocks();
        $list = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('setUser')->andReturnNull();

        // Act
        $jon->addShoppingList($list);

        // Assert
        $this->assertContains($list, $jon->getShoppingLists());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_remove_a_shopping_list(): void {
        // Arrange
        $listToKeep   = Mockery::mock(ShoppingList::class)
            ->shouldReceive([
                'equals'    => true,
                'setUser' => null
            ])->getMock();
        $listToRemove = Mockery::mock(ShoppingList::class)
            ->shouldReceive([
                'equals'    => true,
                'setUser' => null
            ])->getMock();
        $jon = UserBuilder::newWithMocks()
            ->withShoppingList($listToKeep)
            ->withShoppingList($listToRemove)
            ->build();

        // Act
        $jon->removeList($listToRemove);

        // Assert
        $this->assertContains($listToKeep, $jon->getShoppingLists());
        $this->assertNotContains($listToRemove, $jon->getShoppingLists());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_add_a_product_to_a_shopping_list(): void {
        // Arrange
        $sugar = Mockery::mock(WishedProduct::class);
        $list  = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('addProduct')->with($sugar)->once();
        $list->shouldReceive('getProducts')->andReturn(new ArrayCollection([$sugar]))->once();
        $jon = UserBuilder::newWithMocks()->withShoppingList($list)->build();

        // Act
        $jon->addProduct($sugar, $list);

        // Assert
        $this->assertContains($sugar, $jon->getShoppingLists()->first()->getProducts());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_remove_a_product_from_a_shopping_list(): void {
        // Arrange
        $sugar  = Mockery::mock(WishedProduct::class);
        $coffee = Mockery::mock(WishedProduct::class);
        $list = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('addProduct')->with($sugar)->once();
        $list->shouldReceive('addProduct')->with($coffee)->once();
        $list->shouldReceive('removeProduct')->with($sugar)->once();
        $list->shouldReceive('getProducts')->andReturn(new ArrayCollection([$coffee]))->twice();
        $jon = UserBuilder::newWithMocks()->withShoppingList($list)->build();

        // Act
        $jon->addProduct($sugar, $list);
        $jon->addProduct($coffee, $list);
        $jon->removeProduct($sugar, $list);

        // Assert
        $this->assertContains($coffee, $jon->getShoppingLists()->first()->getProducts());
        $this->assertNotContains($sugar, $jon->getShoppingLists()->first()->getProducts());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_add_a_threshold(): void {
        // Arrange
        $threshold = Mockery::mock(GeneralThreshold::class);
        $jon = UserBuilder::anyUserBuiltWithMocks();

        // Act
        $jon->addThreshold($threshold);

        // Assert
        $this->assertContains($threshold, $jon->getThresholds());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_remove_a_threshold(): void {
        // Arrange
        $thresholdToKeep   = Mockery::mock(GeneralThreshold::class);
        $thresholdToRemove = Mockery::mock(CategoryThreshold::class);
        $jon = UserBuilder::newWithMocks()
            ->withThreshold($thresholdToKeep)
            ->withThreshold($thresholdToRemove)
            ->build();

        // Act
        $jon->removeThreshold($thresholdToRemove);

        // Assert
        $this->assertContains($thresholdToKeep, $jon->getThresholds());
        $this->assertNotContains($thresholdToRemove, $jon->getThresholds());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_go_to_the_market_with_a_wished_shopping_list(): void {
        // Arrange
        $list = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('markAsMarket')->once();
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('isMarketList')->once()->andReturn(true);
        $jon = UserBuilder::newWithMocks()->withShoppingList($list)->build();

        // Act
        $jon->goToTheMarket($list);

        // Assert
        $this->assertTrue($jon->getShoppingLists()->first()->isMarketList());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_check_product_from_list_when_is_at_market(): void {
        // Arrange
        $coffee = Mockery::mock(WishedProduct::class);
        $coffee->shouldReceive('isOnCart')->andReturn(true)->once();
        $list = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('markAsMarket')->once();
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('addProduct')->with($coffee)->once();
        $list->shouldReceive('addToCart')->with($coffee)->once();
        $list->shouldReceive('getWishProducts')->andReturn(new ArrayCollection([$coffee]))->once();
        $jon = UserBuilder::newWithMocks()->withShoppingList($list)->build();

        // Act
        $jon->addProduct($coffee, $list);
        $jon->goToTheMarket($list);
        $jon->checkProduct($coffee, $list);

        // Assert
        $this->assertTrue($jon->getShoppingLists()->first()->getWishProducts()->first()->isOnCart());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_uncheck_product_from_list_when_is_at_market(): void {
        // Arrange
        $coffee = Mockery::mock(WishedProduct::class);
        $coffee->shouldReceive('isWished')->andReturn(true)->once();
        $list = Mockery::mock(ShoppingList::class);
        $list->shouldReceive('markAsMarket')->once();
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('addProduct')->with($coffee)->once();
        $list->shouldReceive('addToCart')->with($coffee)->once();
        $list->shouldReceive('removeFromCart')->with($coffee)->once();
        $list->shouldReceive('getWishProducts')->andReturn(new ArrayCollection([$coffee]))->once();
        $jon = UserBuilder::newWithMocks()->withShoppingList($list)->build();

        // Act
        $jon->addProduct($coffee, $list);
        $jon->goToTheMarket($list);
        $jon->checkProduct($coffee, $list);
        $jon->uncheckProduct($coffee, $list);

        // Assert
        $this->assertTrue($jon->getShoppingLists()->first()->getWishProducts()->first()->isWished());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_request_for_a_box_when_is_ready_and_get_estimated_time(): void {
        // Arrange
        $list   = Mockery::mock(ShoppingList::class);
        $market = Mockery::mock(Market::class);
        $market->shouldReceive('estimatedWaitingTime')->with($list)->andReturn(5);
        $jon = UserBuilder::newWithMocks()->withMarket($market)->build();

        // Act
        $time = $jon->requestBox($list);

        // Assert
        $this->assertEquals(5, $time); // minutes
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_go_to_the_box_when_is_called(): void {
        // Arrange
        $box    = Mockery::mock(Box::class);
        $market = Mockery::mock(Market::class);
        $list   = Mockery::mock(ShoppingList::class);
        $jon    = UserBuilder::newWithMocks()->withMarket($market)->build();
        $market->shouldReceive('goingToBox')->once()->withArgs([$box, $jon, $list]);

        // Act
        $jon->goToTheBox($box, $list);

        // Assert
        // mmm... nothing to assert?
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_buy_a_list(): void {
        // Arrange
        $box    = Mockery::mock(Box::class);
        $market = Mockery::mock(Market::class);
        $list   = Mockery::mock(ShoppingList::class);
        $jon    = UserBuilder::newWithMocks()->withMarket($market)->build();
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('markAsPurchased')->once()->withNoArgs();
        $market->shouldReceive('purchaseMade')->once()->withArgs([$box, $jon, $list]);

        // Act
        $jon->buyList($box, $list);

        // Assert
        // mmm... nothing to assert?
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_request_list_for_delivery(): void {
        // Arrange
        $box    = Mockery::mock(Box::class);
        $market = Mockery::mock(Market::class);
        $list   = Mockery::mock(ShoppingList::class);
        $jon    = UserBuilder::newWithMocks()->withMarket($market)->build();
        $list->shouldReceive('setUser')->andReturnNull();
        $list->shouldReceive('markAsDelivery')->once()->withNoArgs();
        $market->shouldReceive('deliveryRequest')->once()->withArgs([$box, $jon, $list]);

        // Act
        $jon->requestForDelivery($box, $list);

        // Assert
        // mmm... nothing to assert?
    }
}
