<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\ClientService;
use Thales\PhpBanking\resources\Response;
use Exception;

class ClientController
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function handleRequest($method, $uri): void
    {
        $param = $uri[2] ?? null;
        $id = (is_numeric($param)) ? (int)$param : null;
        $action = (!is_numeric($param)) ? $param : null;
        try {
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $client = $this->clientService->getClient($id);
                        Response::sendJson($client);
                    } elseif ($action === 'create') {
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Client/create.phtml";
                    } elseif ($action === 'login') {
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Client/login.phtml";
                    } elseif ($action === 'home') {
                        $data = $this->clientService->getClientAccounts();
                        Response::sendJson($data, 200);
                    } elseif ($action === 'auth') {
                        header('Content-Type: text/html; charset=utf-8');
                        require __DIR__ . "/../View/Client/home.phtml";
                    } else {
                        $clients = $this->clientService->getAllClients();
                        Response::sendJson($clients);
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);

                    if ($action == 'login') {
                        $client = $this->clientService->login($data['username'], $data['password']);
                        Response::sendJson($client, 200);
                    } else {
                        if (!isset($data['username'], $data['password'], $data['name'], $data['cpfcnpj'], $data['email'])) {
                            Response::sendError(
                                'Dados obrigatórios ausentes (username, password, name, cpfcnpj, email)',
                                400
                            );
                        }

                        $client = $this->clientService->createClient(
                            $data['username'],
                            $data['password'],
                            $data['name'],
                            $data['cpfcnpj'],
                            $data['email']
                        );

                        if (!$client) {
                            Response::sendError('Erro ao criar cliente.', 500);
                        }

                        Response::sendJson([
                            'message' => 'Cliente cadastrado com sucesso!',
                            'client' => $client
                        ], 201);
                    }
                    break;

                case 'PUT':
                    if (!$id) {
                        Response::sendError('Campos obrigatórios não informados!', 400);
                    }

                    $data = json_decode(file_get_contents('php://input'), true);

                    if (!isset($data['username'], $data['password'], $data['name'], $data['email'])) {
                        Response::sendError('Campos obrigatórios não informados!', 400);
                    }

                    $updated = $this->clientService->updateClient(
                        $id,
                        $data['username'],
                        $data['password'],
                        $data['name'],
                        $data['email']
                    );

                    if (!$updated) {
                        Response::sendError("Erro ao atualizar cliente com ID $id !", 500);
                    }

                    Response::sendJson([
                        'message' => 'Cliente atualizado com sucesso!',
                        'client' => $updated
                    ]);
                    break;

                default:
                    Response::sendError('Método não permitido!', 405);
                    break;
            }
        } catch (Exception $e) {
            Response::sendError('Erro inesperado no servidor!', 500, $e->getMessage());
        }
    }
}
