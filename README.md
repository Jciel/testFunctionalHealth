# Test Functional Health API

API desenvolvida em Laravel para gerenciamento de contas, permitindo operações de depósito, saque e consulta de saldo.

---

## Dependências

Antes de iniciar, você precisa ter instalado:

- Docker
- Docker Compose
- Make (opcional, mas recomendado)

---

## Instalação

### 1. Clone o repositório

```bash
git clone <repo-url>
cd test-functional-health
```

### 2. Suba os containers
```bash
docker-compose up --build
```
Ou com Make
```bash
make d-up-build
```

### 3. Instale as dependências do Laravel
```bash
docker exec -it test-func-health composer install
```
Ou com Make
```bash
make d-install
```

### 4. Instale as dependências do Laravel
```bash
docker exec -it test-func-health php artisan key:generate
```

### 5. Configure o ambiente
```bash
cp .env.example .env
```
Ajuste as variáveis de banco se necessário:
```
DB_CONNECTION=pgsql
DB_HOST=dbfunchealth
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 6. Execute as migrations
```bash
docker exec -it test-func-health php artisan migrate
```
Ou
```bash
make migrate
```

<br>

A API estará disponível em:
```
http://localhost:8000
```

<br>
<br>

## Endpoints da API

<details>
<summary>Depositar</summary>

> POST /api/v1/accounts/{account}/deposit

body:
```json
{
  "amount": 500,
  "currency": "BRL"
}
```

</details>

<details>
<summary>Sacar</summary>

> POST /api/v1/accounts/{account}/withdraw

body:
```json
{
  "amount": 500,
  "currency": "BRL"
}
```

</details>

<details>
<summary>Consultar saldo</summary>

> GET /api/v1/accounts/{account}/balance

body:
```json
```

</details>

<br>
<br>

## Executando testes

### Rodar todos os testes
```bash
docker-compose exec app vendor/bin/phpunit
```
Ou
```bash
make test
```

### Rodar um teste específico
```bash
make testfile file=AccountServiceTest
```

<br>

## Comandos úteis (Makefile)

| Comando              | Descrição                |
|----------------------|--------------------------|
| make d-up            | Sobe os containers       |
| make d-down          | Para os containers       |
| make d-build         | Build sem cache          |
| make d-up-build      | Build + up               |
| make d-install       | Instala dependências     |
| make migrate         | Executa migrations       |
| make migrate-refresh | Recria banco             |
| make db-seeder       | Executa seeders          |
| make test            | Executa testes           |

<br>

## Estrutura do projeto
```
app/
 ├── Models/
 ├── Http/
 │   ├── Controllers/
 │   └── Services/
 ├── Casts/
database/
 ├── migrations/
 ├── factories/
tests/
 ├── Unit/
 └── Feature/
docker/
 └── nginx/
 ```

## Problemas comuns

### Erro de permissão
```bash
 chmod -R 777 storage bootstrap/cache
```

### Vendor não encontrado
```bash
docker exec -it test-func-health composer install
```

### Erro de cache
```bash
docker exec -it test-func-health php artisan config:clear
docker exec -it test-func-health php artisan cache:clear
```

### Observações
A aplicação utiliza Money PHP para controle de valores monetários   
Todos os valores são tratados em centavos e em integer   
Operações são registradas na tabela operations   

