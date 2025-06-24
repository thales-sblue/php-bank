<?php

namespace Thales\PhpBanking\Service;

use Thales\PhpBanking\Model\Client\ClientRepositoryInterface;
use Thales\PhpBanking\resources\Session;
use Thales\PhpBanking\resources\Response;
use PDOException;
use Exception;

class ClientService
{
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function createClient($username, $password, $name, $cpfcnpj, $email)
    {
        if (empty($username) || empty($name) || empty($cpfcnpj) || empty($email) || empty($password)) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido.");
        }

        if (!preg_match('/^[0-9]{11,14}$/', $cpfcnpj)) {
            throw new Exception("CPF/CNPJ inválido. Deve conter apenas números (11 ou 14 dígitos).");
        }

        try {
            $client =  $this->clientRepository->createClient($username, $password, $name, $cpfcnpj, $email);
            Session::start();
            Session::set('client', [
                'id' => $client['id'],
                'username' => $client['username'],
                'name' => $client['name']
            ]);

            return $client;
        } catch (PDOException $e) {
            Session::destroy();

            if ($e->getCode() === '23505') { // erro de violação de UNIQUE no PostgreSQL
                throw new Exception("Já existe um usuário com dados únicos conflitantes.");
            }

            throw $e;
        }
    }

    public function getClient($id)
    {
        $client = $this->clientRepository->getClient($id);
        if (!$client) {
            throw new Exception("Usuário não encontrado.");
        }
        return $client;
    }

    public function getAllClients()
    {
        return $this->clientRepository->getAllClients();
    }

    public function updateClient($id, $username, $password, $name, $email)
    {
        $client = $this->clientRepository->getClient($id);
        if (!$client) {
            throw new Exception("Usuário não encontrado para atualização.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido.");
        }

        return $this->clientRepository->updateClient($id, $username, $password, $name, $email);
    }

    public function login(string $username = '', string $password = '')
    {
        if (!$username || !$password) {
            throw new Exception("usuario e senha sao obrigatorios");
        }

        $client = $this->clientRepository->getClient($username);
        if (!$client) {
            throw new Exception("usuario não existe");
        }

        if (!$client || !password_verify($password, $client['password'])) {
            throw new Exception("usuario ou senha incorretos");
        }

        Session::start();
        Session::set('client', [
            'id' => $client['id'],
            'username' => $client['username'],
            'name' => $client['name']
        ]);

        return $client;
    }

    public function getClientAccounts()
    {
        $client = Session::get('client') ?? '';
        if (!$client) {
            Session::destroy();
        }
        $clientId = $client['id'] ?? '';
        $data = $this->clientRepository->getClientAccounts($clientId);

        $client = [
            'id' => $clientId,
            'username' => $data[0]['username'],
            'name'     => $data[0]['name'],
            'cpfcnpj'  => $data[0]['cpfcnpj'],
            'email'    => $data[0]['email'],
        ];

        $accounts = [];

        foreach ($data as $row) {
            if (!empty($row['id'])) {
                $accounts[] = [
                    'id'      => $row['id'],
                    'balance' => (float) ($row['balance'] ?? 0),
                    'type'    => $row['type'] ?? '',
                    'active'  => (bool) ($row['active'] ?? false),
                ];
            }
        }

        $response = [
            'client'   => $client ?? '',
            'accounts' => $accounts ?? ''
        ];

        if (!$response) {
            throw new Exception("usuario nao possui conta criada");
        }

        return $response;
    }
}
