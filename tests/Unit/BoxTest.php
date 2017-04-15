<?php

namespace Tests\Unit;

use App\Model\Box;
use App\Model\BoxTime;
use Carbon\Carbon;
use Tests\TestCase;

class BoxTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_has_a_number(): void
    {
        // Arrange
        $box = new Box(1, true);

        // Assert
        $this->assertEquals(1, $box->getNumber());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_disabled(): void
    {
        // Arrange
        $box = new Box(1, true);

        // Act
        $box->disable();

        // Assert
        $this->assertFalse($box->isEnabled());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_enabled(): void
    {
        // Arrange
        $box = new Box(1, false);

        // Act
        $box->enable();

        // Assert
        $this->assertTrue($box->isEnabled());
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_an_estimated_waiting_time(): void
    {
        // Arrange
        $box = new Box(1, true);

        // Act
        // Fixme: no puedo pasarle el mock al addBoxTime. $boxTime = Mockery::mock(BoxTime::class)->shouldReceive('estimatedWaitingTime')->andReturn(50)->getMock();
        $box->addBoxTime(new BoxTime(Carbon::now(), 20));
        $box->addBoxTime(new BoxTime(Carbon::now(), 50));

        // Assert
        $this->assertEquals(35, $box->estimatedWaitingTime());
    }
}
