<?php
require_once __DIR__ . '/../Service/ClientService.php';

class ClientController
{
    private $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService();
    }

    public function handlerequest($method, $uri)
    {
        $id = isset($uri[2]) ? (int)$uri[2] : null;

        switch ($method) {
            case 'GET':
                try {
                    if ($id) {
                        $client = $this->clientService->getClient($id);
                        echo json_encode($client, JSON_UNESCAPED_UNICODE);
                    } else {
                        $client = $this->clientService->getAllClients();
                        echo json_encode($client, JSON_UNESCAPED_UNICODE);
                    }
                } catch (Exception $e) {
                    http_response_code(404);
                    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                try {
                    if (isset($data['username'], $data['password'], $data['name'], $data['cpfcnpj'], $data['email'])) {
                        $this->clientService->createClient(
                            $data['username'],
                            $data['password'],
                            $data['name'],
                            $data['cpfcnpj'],
                            $data['email']
                        );
                        http_response_code(201);
                        echo json_encode(['message' => 'Cliente criado com sucesso'], JSON_UNESCAPED_UNICODE);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Dados obrigatórios ausentes'], JSON_UNESCAPED_UNICODE);
                    }
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                break;

            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents('php://input'), true);
                    try {
                        if (isset($data['username'], $data['password'], $data['name'], $data['email'])) {
                            $this->clientService->updateClient(
                                $id,
                                $data['username'],
                                $data['password'],
                                $data['name'],
                                $data['email']
                            );
                            echo json_encode(['message' => 'Cliente atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Dados obrigatórios ausentes para atualização'], JSON_UNESCAPED_UNICODE);
                        }
                    } catch (Exception $e) {
                        http_response_code(400);
                        echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID do cliente é obrigatório para atualização'], JSON_UNESCAPED_UNICODE);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }
}
