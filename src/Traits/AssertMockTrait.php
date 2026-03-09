<?php

namespace WHMCS\Module\Addon\Cloud4Africa\Traits;

trait AssertMockTrait
{
    protected function assertHttpSuccess(\Symfony\Component\HttpFoundation\Response $response, ?int $statusCode = null): void
    {
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $response);

        if ($statusCode) {
            $this->assertEquals($statusCode, $response->getStatusCode());
        } else {
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    protected function assertHttpError(\Symfony\Component\HttpFoundation\Response $response, ?int $statusCode = null): void
    {
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Response::class, $response);

        if ($statusCode) {
            $this->assertEquals($statusCode, $response->getStatusCode());
        } else {
            $this->assertGreaterThanOrEqual(400, $response->getStatusCode());
            $this->assertLessThan(600, $response->getStatusCode());
        }
    }

    protected function assertRedirectTo(
        \Symfony\Component\HttpFoundation\Response $response,
        string $expectedUrl
    ): void {
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\RedirectResponse::class, $response);
        $this->assertEquals($expectedUrl, $response->getTargetUrl());
    }
}
