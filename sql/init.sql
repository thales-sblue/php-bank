CREATE TABLE client (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    cpfcnpj VARCHAR(14) NOT NULL UNIQUE CHECK (cpfcnpj ~ '^[0-9]{11,14}$'),
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE account (
    id SERIAL PRIMARY KEY,
    client_id INT REFERENCES client(id) ON DELETE CASCADE,
    balance NUMERIC(14,2) NOT NULL DEFAULT 0.00,
    type VARCHAR(50) NOT NULL CHECK (type IN ('corrente', 'poupanca')),
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transfer (
  id               SERIAL PRIMARY KEY,
  from_account_id  INT NOT NULL REFERENCES account(id) ON DELETE RESTRICT,
  to_account_id    INT NOT NULL REFERENCES account(id) ON DELETE RESTRICT,
  amount           NUMERIC(14,2) NOT NULL,
  status           VARCHAR(20)   NOT NULL CHECK (status IN ('pending','completed','failed')),
  created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  executed_at      TIMESTAMP     NULL
);

CREATE TABLE account_transaction (
    id SERIAL PRIMARY KEY,
    account_id INT REFERENCES account(id) ON DELETE CASCADE,
    amount NUMERIC(14,2) NOT NULL,
    type VARCHAR(50) NOT NULL CHECK (type IN ('deposito', 'saque')),
    transfer_id INT REFERENCES transfer(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


