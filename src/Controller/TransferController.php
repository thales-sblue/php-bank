<?php

require_once __DIR__ . '/../Service/TransferService.php';
require_once __DIR__ . '/../Utils/Response.php';

class TransferController
{
    private $transferService;

    public function __construct()
    {
        $this->transferService = new TransferService();
    }

    public function handleRequest($method, $uri)
    {
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $transfers = $this->transferService->getTransfersByAccount($id);
                        sendJson($transfers);
                    } else {
                        $transfers = $this->transferService->getAllTransfers();
                        sendJson($transfers);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if (!isset($data['fromAccountId'], $data['toAccountId'], $data['amount'])) {
                        sendError('Dados obrigatórios ausentes (fromAccountId, toAccountId, amount)', 400);
                    }

                    $transfer = $this->transferService->processTransfer(
                        $data['fromAccountId'],
                        $data['toAccountId'],
                        $data['amount']
                    );

                    sendJson([
                        'message' => 'Transferência realizada com sucesso.',
                        'transfer' => $transfer
                    ], 201);
                    break;

                default:
                    sendError('Método não permitido', 405);
                    break;
            }
        } catch (Exception $e) {
            sendError('Erro ao processar a transferência', 500, $e->getMessage());
        }
    }
}
