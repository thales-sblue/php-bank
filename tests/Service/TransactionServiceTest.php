<?php

use PHPUnit\Framework\TestCase;
use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\Service\AccountService;
use Thales\PhpBanking\Model\Transaction\TransactionRepositoryInterface;

class TransactionServiceTest extends TestCase
{
    private $transactionRepositoryMock;
    private $accountServiceMock;
    private $transactionService;

    protected function setUp(): void
    {
        $this->transactionRepositoryMock = $this->createMock(TransactionRepositoryInterface::class);
        $this->accountServiceMock = $this->createMock(AccountService::class);

        $this->transactionService = new TransactionService(
            $this->transactionRepositoryMock,
            $this->accountServiceMock
        );
    }

    public function testCreateTransactionWithValidData(): void
    {
        $accountId = 1;
        $amount = 100;
        $type = 'deposito';

        $expectedResult = [
            'id' => 1,
            'account_id' => $accountId,
            'amount' => $amount,
            'type' => $type,
            'transfer_id' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->accountServiceMock
            ->method('processTransaction')
            ->willReturn(true);

        $this->transactionRepositoryMock
            ->method('createTransaction')
            ->with($accountId, $amount, $type, null)
            ->willReturn($expectedResult);

        $result = $this->transactionService->createTransaction($accountId, $amount, $type);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAllTransactionsReturnsArray(): void
    {
        $mockTransactions = [
            ['id' => 1, 'account_id' => 1, 'amount' => 100, 'type' => 'deposito'],
            ['id' => 2, 'account_id' => 2, 'amount' => 50, 'type' => 'saque']
        ];

        $this->transactionRepositoryMock
            ->method('getAllTransactions')
            ->willReturn($mockTransactions);

        $result = $this->transactionService->getAllTransactions();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
