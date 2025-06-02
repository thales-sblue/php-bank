<?php

use PHPUnit\Framework\TestCase;
use Thales\PhpBanking\Service\TransferService;
use Thales\PhpBanking\Model\Transfer\TransferRepositoryInterface;
use Thales\PhpBanking\Service\TransactionService;
use Thales\PhpBanking\Service\AccountService;

class TransferServiceTest extends TestCase
{
    private $transferRepositoryMock;
    private $transactionServiceMock;
    private $accountServiceMock;
    private $transferService;

    protected function setUp(): void
    {
        $this->transferRepositoryMock = $this->createMock(TransferRepositoryInterface::class);
        $this->transactionServiceMock = $this->createMock(TransactionService::class);
        $this->accountServiceMock = $this->createMock(AccountService::class);

        $this->transferService = new TransferService(
            $this->transferRepositoryMock,
            $this->transactionServiceMock,
            $this->accountServiceMock
        );
    }

    public function testCreateTransferWithValidDataReturnsTransferArray()
    {
        $from = 1;
        $to = 2;
        $amount = 100;

        $expectedTransfer = ['id' => 1, 'fromAccountId' => $from, 'toAccountId' => $to, 'amount' => $amount];

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('createTransfer')
            ->with($from, $to, $amount)
            ->willReturn($expectedTransfer);

        $result = $this->transferService->createTransfer($from, $to, $amount);

        $this->assertEquals($expectedTransfer, $result);
    }

    public function testCreateTransferWithMissingDataThrowsException()
    {
        $this->expectException(Exception::class);
        $this->transferService->createTransfer(null, 2, 100);
    }

    public function testProcessTransferSuccess()
    {
        $from = 1;
        $to = 2;
        $amount = 150;

        $mockTransfer = ['id' => 10, 'fromAccountId' => $from, 'toAccountId' => $to, 'amount' => $amount];

        $this->accountServiceMock->method('getAccount')->willReturn(true);

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('createTransfer')
            ->willReturn($mockTransfer);

        $this->transactionServiceMock
            ->expects($this->once())
            ->method('processTransferTransactions')
            ->with($mockTransfer);

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('updateTransferStatus')
            ->with($mockTransfer['id'], 'completed');

        $result = $this->transferService->processTransfer($from, $to, $amount);

        $this->assertEquals($mockTransfer, $result);
    }

    public function testProcessTransferFailureUpdatesStatusAndThrows()
    {
        $this->expectException(Exception::class);

        $from = 1;
        $to = 2;
        $amount = 200;

        $mockTransfer = ['id' => 99, 'fromAccountId' => $from, 'toAccountId' => $to, 'amount' => $amount];

        $this->accountServiceMock->method('getAccount')->willReturn(true);

        $this->transferRepositoryMock
            ->method('createTransfer')
            ->willReturn($mockTransfer);

        $this->transactionServiceMock
            ->method('processTransferTransactions')
            ->willThrowException(new Exception("Erro ao registrar transação."));

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('updateTransferStatus')
            ->with($mockTransfer['id'], 'failed');

        $this->transferService->processTransfer($from, $to, $amount);
    }

    public function testGetTransfersByAccountCallsRepository()
    {
        $accountId = 123;
        $expected = [['id' => 1], ['id' => 2]];

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('getTransfersByAccount')
            ->with($accountId)
            ->willReturn($expected);

        $result = $this->transferService->getTransfersByAccount($accountId);
        $this->assertEquals($expected, $result);
    }

    public function testGetAllTransfersCallsRepository()
    {
        $expected = [['id' => 1], ['id' => 2]];

        $this->transferRepositoryMock
            ->expects($this->once())
            ->method('getAllTransfers')
            ->willReturn($expected);

        $result = $this->transferService->getAllTransfers();
        $this->assertEquals($expected, $result);
    }
}
