<?php

declare(strict_types=1);

namespace Thales\PhpBanking\Tests\Service;

use PHPUnit\Framework\TestCase;
use Thales\PhpBanking\Service\ClientService;
use Thales\PhpBanking\Model\Client\ClientRepositoryInterface;
use Exception;

class ClientServiceTest extends TestCase
{
    private $clientRepositoryMock;
    private $clientService;

    protected function setUp(): void
    {
        $this->clientRepositoryMock = $this->createMock(ClientRepositoryInterface::class);
        $this->clientService = new ClientService($this->clientRepositoryMock);
    }

    public function testGetClientReturnsClientWhenExists(): void
    {
        $clientData = [
            'id' => 1,
            'username' => 'joaozinho',
            'name' => 'João',
            'cpfcnpj' => '12345678901',
            'email' => 'joao@example.com'
        ];

        $this->clientRepositoryMock
            ->method('getClient')
            ->with(1)
            ->willReturn($clientData);

        $result = $this->clientService->getClient(1);

        $this->assertEquals($clientData, $result);
    }

    public function testGetClientThrowsExceptionWhenNotFound(): void
    {
        $this->clientRepositoryMock
            ->method('getClient')
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Usuário não encontrado.");

        $this->clientService->getClient(999);
    }
}
