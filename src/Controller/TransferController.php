<?php

require_once __DIR__ . '/../Service/TransferService.php';

class TransferController
{
    private $transferService;

    public function __construct()
    {
        $this->transferService = new TransferService();
    }

    public function handleRequest($method, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    echo json_encode($this->transferService->getTransfersByAccount($id), JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode($this->transferService->getAllTransfers(), JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);

                if (!isset($data['fromAccountId'], $data['toAccountId'], $data['amount'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Dados obrigatórios ausentes (fromAccountId, toAccountId, amount)'], JSON_UNESCAPED_UNICODE);
                    return;
                }

                try {
                    $transfer = $this->transferService->processTransfer(
                        $data['fromAccountId'],
                        $data['toAccountId'],
                        $data['amount']
                    );
                    echo json_encode($transfer, JSON_UNESCAPED_UNICODE);
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
}
