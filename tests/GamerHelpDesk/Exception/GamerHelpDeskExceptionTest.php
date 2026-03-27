<?php

use GamerHelpDesk\Exception\GamerHelpDeskException;
use GamerHelpDesk\Exception\GamerHelpDeskExceptionEnum;
use PHPUnit\Framework\TestCase;

class GamerHelpDeskExceptionTest extends TestCase
{
    public function testHandleException(): void
    {
        $exception = new GamerHelpDeskException(GamerHelpDeskExceptionEnum::SYSTEM_EXCEPTION,"Test exception");
        $this->assertSame("Test exception", $exception->getMessage());
        $this->expectException(GamerHelpDeskException::class);
        throw $exception;
    }

}