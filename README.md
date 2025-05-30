# 💸 PHP Banking API

![PHP](https://img.shields.io/badge/language-PHP-blue)
![Dockerized](https://img.shields.io/badge/docker-ready-0db7ed)
![PostgreSQL](https://img.shields.io/badge/database-PostgreSQL-336791)
![Build](https://img.shields.io/badge/build-passing-brightgreen)
![Hits](https://hits.sh/github.com/thales-blue/php-banking-api.svg)

> Uma API REST para operações bancárias básicas, construída com PHP puro, PostgreSQL e Docker.  
> Criada para estudos, testes de arquitetura e desafios técnicos com controle de saldo e transações seguras.

---

## 🚀 Funcionalidades

- Cadastro de clientes
- Criação de contas do tipo `corrente` ou `poupança`
- Depósito e saque com atualização automática de saldo
- Registro de transações e histórico
- Registro de transferência entre contas
- Validação de saldo para saques
- Docker-ready para subir o ambiente com um comando

---

## 🧰 Tecnologias

- **PHP 8.3**
- **PostgreSQL**
- **Docker / Docker Compose**
- **Apache**
- **PDO**

---

## ⚙️ Como rodar o projeto

```bash
# Clone o repositório
git clone https://github.com/thales-blue/php-banking-api.git
cd php-banking-api

# Suba os containers
docker-compose up --build