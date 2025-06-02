<?php

use PHPUnit\Framework\TestCase;
use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Model\Account\AccountRepositoryInterface;

class AccountServiceTest extends TestCase
{
    private $clientRepositoryMock;
    private $accountService;

    protected function setUp(): void
    {
        $this->clientRepositoryMock = $this->createMock(AccountRepositoryInterface::class);
        $this->accountService = new AccountService($this->clientRepositoryMock);
    }

    public function testCreateAccountSuccess()
    {
        $clientId = 1;
        $balance = 1000.00;
        $type = 'corrente';

        $this->clientRepositoryMock->method('getAccountByClientId')->willReturn(null);
        $this->clientRepositoryMock->method('createAccount')->willReturn([
            'id' => 1,
            'client_id' => $clientId,
            'balance' => $balance,
            'type' => $type
        ]);

        $result = $this->accountService->createAccount($clientId, $balance, $type);

        $this->assertIsArray($result);
        $this->assertEquals($type, $result['type']);
        $this->assertEquals($balance, $result['balance']);
    }

    public function testCreateAccountThrowsIfAccountExists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Já existe uma conta do tipo corrente");

        $clientId = 1;
        $balance = 100;
        $type = 'corrente';

        $this->clientRepositoryMock->method('getAccountByClientId')->willReturn(['existing' => true]);

        $this->accountService->createAccount($clientId, $balance, $type);
    }

    public function testGetAccountThrowsIfNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Conta com ID 1 não encontrada.");

        $this->clientRepositoryMock->method('getAccount')->willReturn(false);

        $this->accountService->getAccount(1);
    }
}
