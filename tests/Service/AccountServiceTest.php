<?php

use PHPUnit\Framework\TestCase;
use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Model\Account\AccountRepositoryInterface;

class AccountServiceTest extends TestCase
{
    private $mockRepository;
    private $accountService;

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(AccountRepositoryInterface::class);
        $this->accountService = new AccountService($this->mockRepository);
    }

    public function testCreateAccountSuccess()
    {
        $clientId = 1;
        $balance = 1000.00;
        $type = 'corrente';

        $this->mockRepository->method('getAccountByClientId')->willReturn(null);
        $this->mockRepository->method('createAccount')->willReturn([
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

        $this->mockRepository->method('getAccountByClientId')->willReturn(['existing' => true]);

        $this->accountService->createAccount($clientId, $balance, $type);
    }

    public function testGetAccountThrowsIfNotFound()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Conta com ID 1 não encontrada.");

        $this->mockRepository->method('getAccount')->willReturn(false);

        $this->accountService->getAccount(1);
    }
}
