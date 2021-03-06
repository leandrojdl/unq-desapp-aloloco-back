<?php
namespace Tests\Integrations;

use App\Model\User;
use App\Model\ShoppingList;
use App\Repository\ShoppingListRepository;

class ShoppingListTest extends IntegrationsTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->repo = resolve(ShoppingListRepository::class);
    }

    /**
     * @test
     * New Shopping List has no products
     *
     * @return void
     */
    public function it_stores_and_retrieves()
    {
        // Arrange
        $jon = entity(User::class)->create([
            'username' => 'jon.snow',
            'email' => 'the.king.in.the.north@seven-kingdoms.org'
        ]);
        entity(ShoppingList::class, 'wish-list')->create([
            'user' => $jon,
            'name'   => 'Jimmy'
        ]);

        // Act
        $repoList = $this->repo->findOneBy(['name' => 'Jimmy']);

        // Assert
        $this->assertEquals(1, $repoList->getId());
        $this->assertEquals('Jimmy', $repoList->getName());
        $this->assertEquals(ShoppingList::class . "(1,Jimmy)", $repoList->__toString());
    }
}