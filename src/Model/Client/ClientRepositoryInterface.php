<?php

namespace Thales\PhpBanking\Model\Client;

interface ClientRepositoryInterface
{
    public function createClient($username, $password, $name, $cpfcnpj, $email);
    public function getClient($param);
    public function getAllClients();
    public function updateClient($id, $username, $password, $name, $email);
    public function getClientAccounts($idClient);
}
