<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\TransferService;
use Thales\PhpBanking\resources\Response;
use Thales\PhpBanking\resources\View;
use Exception;

class TransferController
{
    private TransferService $transferService;

    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function createForm(): void
    {
        View::render('Transfer.create');
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['fromAccountId'], $data['toAccountId'], $data['amount'])) {
            Response::sendError('Campos obrigatórios não informados!', 400);
        }

        try {
            $transfer = $this->transferService->processTransfer(
                $data['fromAccountId'],
                $data['toAccountId'],
                $data['amount']
            );

            Response::sendJson([
                'message' => 'Transferência realizada com sucesso!',
                'transfer' => $transfer
            ], 201);
        } catch (Exception $e) {
            Response::sendError('Erro ao processar a transferência!', 500, $e->getMessage());
        }
    }
}
