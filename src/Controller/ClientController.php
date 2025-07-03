<?php

namespace Thales\PhpBanking\Controller;

use Thales\PhpBanking\Service\ClientService;
use Thales\PhpBanking\resources\Response;
use Thales\PhpBanking\resources\View;
use Exception;

class ClientController
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index(): void
    {
        $clients = $this->clientService->getAllClients();
        Response::sendJson($clients);
    }

    public function show($id): void
    {
        $client = $this->clientService->getClient($id);
        Response::sendJson($client);
    }

    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username'], $data['password'], $data['name'], $data['cpfcnpj'], $data['email'])) {
            Response::sendError('Dados obrigatórios ausentes (username, password, name, cpfcnpj, email)', 400);
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

    public function update($id): void
    {
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
            Response::sendError("Erro ao atualizar cliente com ID $id!", 500);
        }

        Response::sendJson([
            'message' => 'Cliente atualizado com sucesso!',
            'client' => $updated
        ]);
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username'], $data['password'])) {
            Response::sendError('Username e password são obrigatórios.', 400);
        }

        $client = $this->clientService->login($data['username'], $data['password']);
        Response::sendJson($client, 200);
    }

    public function loginForm(): void
    {
        View::render('Client.login');
    }

    public function createForm(): void
    {
        View::render('Client.create');
    }

    public function homeData(): void
    {
        $data = $this->clientService->getClientAccounts();
        Response::sendJson($data, 200);
    }

    public function homePage(): void
    {
        View::render('Client.home');
    }
}
