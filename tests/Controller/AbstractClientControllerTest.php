<?php

namespace WHMCS\Cloud4Africa\Tests\Controller;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\TestCase;
use WHMCS\Cloud4Africa\Client\KarajanClientInterface;
use WHMCS\Cloud4Africa\Repository\WhmcsRepositoryInterface;
use WHMCS\Cloud4Africa\Service\TemplateManagerInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Translation\TranslatorInterface;
use WHMCS\Cloud4Africa\Tests\Traits\AssertMockTrait;
use WHMCS\Cloud4Africa\Tests\Traits\ConfigTemplateMockTrait;
use WHMCS\Cloud4Africa\Controller\ControllerInterface;

abstract class AbstractClientControllerTest extends TestCase
{
    use AssertMockTrait;
    use ConfigTemplateMockTrait;

    protected WhmcsRepositoryInterface $whmcsRepository;

    protected TranslatorInterface $translator;

    protected KarajanClientInterface $karajanClient;

    protected \Smarty $mockSmarty;

    protected TemplateManagerInterface $templateManager;
    
    protected array $vars;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->whmcsRepository = $this->createMock(WhmcsRepositoryInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->mockSmarty = $this->createMock(\Smarty::class);
        $this->karajanClient = $this->createMock(KarajanClientInterface::class);
        $this->templateManager = $this->createMock(TemplateManagerInterface::class);
    }

    protected function makeApiErrorCallback(): callable
    {
        return function ($method, $url, $options = []) {
            throw new RequestException(
                sprintf('API error on %s %s', $method, $url),
                new Request('GET', $url),
                new Response(500, [], 'API error')
            );
        };
    }

    protected function makeKarajanManagerError(string $method): callable
    {
        throw new RequestException(
            sprintf('KarajanManager error on %s', $method),
            new Request('GET', $url),
            new Response(500, [], 'API error')
        );
    }

    protected function makeGeneralError(string $method): callable
    {
        throw new RequestException(
            sprintf('Error on %s', $method),
            new Request('GET', $url),
            new Response(500, [], 'Error')
        );
    }

    protected function makeController(): ControllerInterface
    {}
}
