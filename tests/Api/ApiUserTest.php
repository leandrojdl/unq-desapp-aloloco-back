<?php

namespace Tests\Api;

use App\Model\Product;
use App\Model\User;
use App\Model\ShoppingList;

/**
 * Class ApiUserTest
 * @package Api
 */
class ApiUserTest extends ApiTestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_get_user_info()
    {
        // Arrange
        $jon = entity(User::class)->create();

        // Act
        $response = $this->get(apiRoute('user.info', ['id' => $jon->getId()]));

        // Assert
        $response->assertJsonFragment(['email' => $jon->getEmail()]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_get_user_wish_lists()
    {
        // Arrange
        $jon  = entity(User::class)->create();
        $list = entity(ShoppingList::class, 'wish-list')->create([
            'user' => $jon
        ]);

        // Act
        $response = $this->get(apiRoute('user.wishlists', ['id' => $jon->getId()]));

        // Assert
        $response->assertJsonFragment([
            'id' => $list->getId()
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_get_user_shopping_history()
    {
        // Arrange
        $arya = entity(User::class)->create(['email' => 'nobody@nowhere']);
        $list = entity(ShoppingList::class, 'wish-list')->create([
            'user' => $arya,
            'name' => 'Offers to Many-Faced God'
        ]);
        $list->markAsPurchased();

        // Act
        $response = $this->get(apiRoute('user.history', ['id' => $arya->getId()]));

        // Assert
        $response->assertJsonFragment(['email' => 'nobody@nowhere']);
        $response->assertJsonFragment(['name'  => 'Offers to Many-Faced God']);
    }
    
    /**
     * @test
     * 
     * @return void
     */
    public function it_ask_for_box_and_can_get_box_or_waiting_time(): void {
        // Arrange
        $arya = entity(User::class)->create(['email' => 'nobody@nowhere']);
        $list = entity(ShoppingList::class, 'wish-list')->create([
            'user' => $arya,
            'name' => 'Offers to Many-Faced God'
        ]);
        $sword = entity(Product::class)->create(['name' => 'Espada']);
        $wine  = entity(Product::class)->create(['name' => 'Vino']);
        $this->addProductToList($list, $sword, 2);
        $this->addProductToList($list, $wine, 10);

        // Act
        $box = $this->api->get("user/{$arya->getId()}/shopping-list/{$list->getId()}/box");

        // Assert
        if ($box['status']== 'ok') {
            $this->assertRegExp('/Pase por caja \d+/', $box['message']);
        }
        if ($box['status']== 'wait') {
            $this->assertRegExp('/Tiempo de espera: \d+ minutos/', $box['message']);
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_auth_by_token()
    {
        // Act
        $response = $this->post(apiRoute('user.auth'), [
            'token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjdjNGRhYWNiMzY5ZDY3NzYzZjcyMjIzYjA3NGQ0ZDEzN2JlNjhmYzgifQ.eyJhenAiOiI1OTAyOTU1MjA2ODctZ29wOGhxNDYzdjMwcDU4bjU5anQxbnFvYWh1a291Z3MuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI1OTAyOTU1MjA2ODctZ29wOGhxNDYzdjMwcDU4bjU5anQxbnFvYWh1a291Z3MuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTU1NTQyNDg2NDA3MDU4NzU0NzQiLCJlbWFpbCI6ImFsYW5tYXRrb3Jza2lAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiI2d3h2RTdhMDlfWVJic2JPdHlDc0l3IiwiaXNzIjoiYWNjb3VudHMuZ29vZ2xlLmNvbSIsImlhdCI6MTQ5Njc4OTc5NywiZXhwIjoxNDk2NzkzMzk3LCJuYW1lIjoiQWxhbiBNLiIsInBpY3R1cmUiOiJodHRwczovL2xoNC5nb29nbGV1c2VyY29udGVudC5jb20vLXVQVmZkWXdxSy13L0FBQUFBQUFBQUFJL0FBQUFBQUFBQVdZL0hybWRsQmlmLUhrL3M5Ni1jL3Bob3RvLmpwZyIsImdpdmVuX25hbWUiOiJBbGFuIiwiZmFtaWx5X25hbWUiOiJNLiIsImxvY2FsZSI6ImVzIn0.mSdRbv-Y0k969Bf2BnCBr6UBlOBkH_7yjDVR-vTGL2id2MwrcGkFCylmhKdiriVoOYbpCE08hCU3-wCAHTDPBpDaK1cOux6hAaSZjzlqb5GjTnZw5vDK40_SZjIFOTWygwb6Cc7gOqUfOKwfth1s2fCMNUw2QapxJXcMwLQiIJaIPBW2cT8zgFOVJBWfnmE-Bo1Y0P7GRx_Lde9hTMXA2V7OgDefg-Fw2idISRXgoqqMhGweikPx8pWwtmq_K6gvtW-OeCSi9HjeVl_W0iERzvxl4IIq9KtZbbwLkKnHuHRKaLyVNRU0rzYcyFtrR598kW7EMrz9SSqGsy77M_t7lw'
        ]);

        // Assert
        $response->assertJson([
            'message' => "Invalid Token",
            'status_code' => 401
        ]);
    }
}
