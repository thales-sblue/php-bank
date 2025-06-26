# 💸 PHP Bank

![PHP](https://img.shields.io/badge/language-PHP-blue)
![Dockerized](https://img.shields.io/badge/docker-ready-0db7ed)
![PostgreSQL](https://img.shields.io/badge/database-PostgreSQL-336791)
![Build](https://img.shields.io/badge/build-passing-brightgreen)
![Hits](https://hits.sh/github.com/thales-blue/php-banking-api.svg)

> Um sistema para operações bancárias básicas, construída com PHP, VueJs, PostgreSQL e Docker.
> Criada para estudos, testes de arquitetura e desafios técnicos com controle de saldo e registro de transações.

---

## 🚀 Funcionalidades

- Cadastro de clientes
- Criação de contas do tipo `corrente` ou `poupança`
- Depósito, saque e transferências com atualização automática de saldo
- Registro de transações e histórico
- Registro de transferência entre contas
- Validação de saldo para saques
- Extrato de transações e transferências
- Testes unitários com PHPUnit para garantir a qualidade do código
- Docker-ready para subir o ambiente com um comando

## 📌 TODO

- Implementar autenticação JWT para proteger rotas e sessões
- Incluir middlewares para validação e autenticação

---

## 🧰 Tecnologias

- **PHP 8.3**
- **VueJs**
- **PostgreSQL**
- **PDO**
- **Docker / Docker Compose**
- **Apache**
- **PHPUnit**

---

## ⚙️ Como rodar o projeto

```bash
# Clone o repositório
git clone https://github.com/thales-blue/php-banking-api.git
cd php-banking-api

# Suba os containers
docker-compose up --build

# Acesse o sistema
http://localhost:8000/
```
