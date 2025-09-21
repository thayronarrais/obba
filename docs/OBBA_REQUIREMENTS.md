# Documentação de Requisitos - Sistema OBBA

## 📋 Visão Geral

**OBBA** é um sistema de gestão financeira e contabilística desenvolvido em Laravel para empresas portuguesas. O sistema permite a gestão de faturas (despesas e vendas), controle de quilometragem, gestão de salários e administração de empresas e utilizadores.

### Módulos Principais
1. **Gestão de Faturas** - Upload e gestão de faturas de despesas e vendas
2. **Gestão de Quilometragem** - Registo de deslocações e quilómetros
3. **Gestão de Salários** - Controle de salários e subsídios
4. **Gestão de Empresas** - Cadastro de empresas
5. **Gestão de Utilizadores** - Administração de utilizadores e permissões
6. **Dashboard** - Visualização de estatísticas e métricas

---

## 🗄️ Estrutura da Base de Dados

### 1. Tabela `users`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | bigint | Sim | Chave primária |
| name | string | Sim | Nome do utilizador |
| email | string (unique) | Sim | Email do utilizador |
| email_verified_at | timestamp | Não | Data de verificação do email |
| password | string | Sim | Password encriptada |
| role | tinyint | Sim | Papel do utilizador (enum UserRole) |
| remember_token | string | Não | Token de sessão |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

#### Enum UserRole
- `ADMIN = 1` - Administrador
- `ACCOUNTANT = 2` - Contabilista
- `USER = 3` - Utilizador

### 2. Tabela `companies`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | bigint | Sim | Chave primária |
| name | string | Sim | Nome da empresa |
| nif | string (unique) | Sim | NIF da empresa |
| address | string | Não | Morada da empresa |
| location | integer | Sim | Localização (enum CompanyLocation) |
| iva_monthly_period | boolean | Sim | Período mensal de IVA |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

#### Enum CompanyLocation
- `MAINLAND = 0` - Portugal Continental
- `AZORES = 1` - Açores
- `MADEIRA = 2` - Madeira

### 3. Tabela `invoice_categories`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------||
| id | bigint | Sim | Chave primária |
| code | string | Sim | Código único da categoria |
| name | string | Sim | Nome da categoria |
| group | string | Sim | Grupo da categoria |
| tax_deductible | boolean | Sim | Se é dedutível |
| active | boolean | Sim | Se está ativa |
| order | integer | Sim | Ordem de exibição |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

### 4. Tabela `invoices`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | bigint | Sim | Chave primária |
| type | string | Sim | Tipo de fatura (enum InvoiceType) |
| category_id | bigint | Não | ID da categoria (FK invoice_categories) |
| atcud | string (unique) | Sim | Código ATCUD da fatura |
| nif | integer | Sim | NIF do emissor |
| date | date | Sim | Data da fatura |
| total_iva | decimal | Sim | Total de IVA |
| total | decimal | Sim | Total da fatura |
| files | string | Sim | Caminho do ficheiro |
| metadata | json | Sim | Metadados da fatura |
| company_id | bigint | Sim | ID da empresa (FK) |
| created_by | bigint | Sim | ID do utilizador criador (FK) |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

#### Enum InvoiceType
- `NONE = 0` - Sem tipo
- `EXPENSE = 1` - Despesa
- `SALE = 2` - Venda

#### Dados da Tabela invoice_categories
A tabela deve ser populada com as seguintes categorias:

| ID | Code | Name | Group | Tax_deductible | Active | Order |
|----|------|------|-------|----------------|--------|-------|
| 1 | NONE | Sem categoria | Geral | false | true | 0 |
| 2 | SUPPLIER | Fornecedor | Geral | true | true | 1 |
| 3 | RENT | Renda | Despesas Operacionais | true | true | 2 |
| 4 | UTILITIES | Água, Luz, Gás | Despesas Operacionais | true | true | 3 |
| 5 | COMMUNICATIONS | Comunicações | Despesas Operacionais | true | true | 4 |
| 6 | GENERAL_MAINTENANCE | Manutenção Geral | Despesas Operacionais | true | true | 5 |
| 7 | VEHICLE_MAINTENANCE | Manutenção de Viaturas | Despesas Operacionais | true | true | 6 |
| 8 | OFFICE_SUPPLIES | Material de Escritório | Despesas Administrativas | true | true | 7 |
| 9 | SOFTWARE_LICENSES | Software e Licenças | Despesas Administrativas | true | true | 8 |
| 10 | CONSULTING_ACCOUNTING | Consultoria / Contabilidade | Despesas Administrativas | true | true | 9 |
| 11 | COMPANY_INSURANCE | Seguros (Empresa) | Despesas Administrativas | true | true | 10 |
| 12 | ADVERTISING | Anúncios | Marketing e Publicidade | true | true | 11 |
| 13 | CONTENT_PRODUCTION | Produção de Conteúdo | Marketing e Publicidade | true | true | 12 |
| 14 | EVENTS_TRADE_SHOWS | Eventos e Feiras | Marketing e Publicidade | true | true | 13 |
| 15 | DESIGN_BRANDING | Design e Branding | Marketing e Publicidade | true | true | 14 |
| 16 | TRAVEL_LODGING | Viagens e Estadias | Viagens e Representação | true | true | 15 |
| 17 | CLIENT_MEALS | Refeições com Clientes | Viagens e Representação | true | true | 16 |
| 18 | EQUIPMENT | Equipamento | Equipamentos e Ativos | true | true | 17 |
| 19 | DEPRECIATION | Depreciações | Equipamentos e Ativos | true | true | 18 |
| 20 | BANK_INTEREST | Juros Bancários | Despesas Financeiras | true | true | 19 |
| 21 | COMMISSIONS | Comissões | Despesas Financeiras | true | true | 20 |
| 22 | FEES | Taxas | Despesas Financeiras | true | true | 21 |
| 23 | NON_DEDUCTIBLE_VAT | IVA não Dedutível | Impostos e Taxas | false | true | 22 |
| 24 | LOCAL_TAXES | Derrama / Contribuições | Impostos e Taxas | true | true | 23 |
| 25 | FINES_PENALTIES | Multas e Coimas | Impostos e Taxas | false | true | 24 |
| 26 | SALARIES | Salários | Despesas com Pessoal | true | true | 25 |
| 27 | SOCIAL_SECURITY | Segurança Social | Despesas com Pessoal | true | true | 26 |
| 28 | SUBSIDIES | Subsídios (férias/Natal) | Despesas com Pessoal | true | true | 27 |
| 29 | TRAINING | Formação | Despesas com Pessoal | true | true | 28 |
| 30 | PERSONAL_VEHICLE_KMS | Kms com Viatura Própria (0,36€/km) | Viaturas e Quilómetros | true | true | 29 |
| 31 | VEHICLE_EXPENSES_UP_TO_20K | Despesas com Viaturas até 20.000€ | Viaturas e Quilómetros | true | true | 30 |
| 32 | VEHICLES_20K_35K | Viaturas >20k-35k / >35k | Viaturas e Quilómetros | true | true | 31 |
| 33 | ELECTRIC_VEHICLES | Viaturas Elétricas | Viaturas e Quilómetros | true | true | 32 |

### 5. Tabela `kilometers`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | bigint | Sim | Chave primária |
| name | string(128) | Sim | Nome do condutor |
| licenseplate | string(12) | Sim | Matrícula do veículo |
| date | date | Sim | Data da viagem |
| origin | string(256) | Sim | Local de origem |
| destination | string(256) | Sim | Local de destino |
| kilometers | integer | Sim | Quilómetros percorridos |
| reason | string(256) | Sim | Motivo da deslocação |
| company_id | bigint | Sim | ID da empresa (FK) |
| created_by | bigint | Sim | ID do utilizador criador (FK) |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

### 6. Tabela `salaries`
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| id | bigint | Sim | Chave primária |
| date | date | Sim | Data do salário |
| employee_id | bigint | Sim | ID do funcionário (FK users) |
| gross_salary_month | decimal | Sim | Salário bruto mensal |
| food_allowance_month | decimal | Sim | Subsídio de alimentação |
| additional_subsidies | decimal | Sim | Subsídios adicionais |
| social_security | decimal | Sim | Segurança social |
| mandatory_ensurance | decimal | Sim | Seguro obrigatório |
| company_id | bigint | Sim | ID da empresa (FK) |
| created_by | bigint | Sim | ID do utilizador criador (FK) |
| created_at | timestamp | Sim | Data de criação |
| updated_at | timestamp | Sim | Data de atualização |

### Tabelas Auxiliares

#### Tabela `password_reset_tokens`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| email | string (primary) | Email do utilizador |
| token | string | Token de reset |
| created_at | timestamp | Data de criação |

#### Tabela `sessions`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | string (primary) | ID da sessão |
| user_id | bigint | ID do utilizador |
| ip_address | string(45) | Endereço IP |
| user_agent | text | User agent |
| payload | longtext | Dados da sessão |
| last_activity | integer | Última atividade |

---

## 📝 Formulários e Validações

### 1. Formulário de Empresa

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| name | Nome da Empresa | text | max:255 | Sim |
| nif | NIF | number | max_digits:12, unique | Sim |
| address | Morada | text | - | Não |
| location | Localização | select | 0-2 | Sim |
| iva_monthly_period | Período Mensal IVA | checkbox | boolean | Sim |

### 2. Formulário de Fatura (Upload Manual)

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| invoiceAtcud | ATCUD | text | unique | Sim |
| invoiceType | Tipo | select | enum(InvoiceType) | Sim |
| invoiceCategoryId | Categoria | select | exists:invoice_categories | Condicional* |
| invoiceCompanyId | Empresa | select | exists:companies | Sim |
| invoiceImage | Ficheiro | file | image, mimes:png | Sim |
| invoiceData | Dados da Fatura | array | array | Sim |

*Obrigatório apenas se invoiceType = EXPENSE

**Estrutura de invoiceData:**
- A: NIF do emissor
- B: NIF do adquirente
- F: Data da fatura
- H: ATCUD
- N: Total IVA
- O: Total

### 3. Formulário de Upload em Massa (PDF)

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| invoices.*.file | Ficheiro PDF | file | mimes:pdf, max:3072 | Sim |
| invoices.*.data | Dados | array | array | Sim |
| invoiceType | Tipo | select | enum(InvoiceType) | Sim |
| invoiceCategoryId | Categoria | select | exists:invoice_categories | Condicional |
| companyId | Empresa | select | exists:companies | Sim |

### 4. Formulário de Quilometragem

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| name | Nome | text | max:256 | Sim |
| licensePlate | Matrícula | text | regex:/[A-Z0-9]{2}-[A-Z0-9]{2}-[A-Z0-9]{2}/ | Sim |
| date | Data | date | date | Sim |
| origin | Origem | text | max:256 | Sim |
| destination | Destino | text | max:256 | Sim |
| kilometers | Quilómetros | number | integer, max_digits:9 | Sim |
| reason | Motivo | text | - | Sim |
| companyId | Empresa | select | exists:companies | Sim |

### 5. Formulário de Salário

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| date | Data | date | date | Sim |
| employeeId | Funcionário | select | exists:users | Sim |
| salary | Salário Bruto | decimal | numeric | Não |
| foodAllowance | Subsídio Alimentação | decimal | numeric | Não |
| additionalSubsidies | Subsídios Adicionais | decimal | numeric | Não |
| socialSecurity | Segurança Social | decimal | numeric | Não |
| ensurance | Seguro Obrigatório | decimal | numeric | Não |
| companyId | Empresa | select | exists:companies | Sim |

### 6. Formulário de Utilizador (Criação)

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| name | Nome | text | max:128 | Sim |
| email | Email | email | unique, max:255 | Sim |
| password | Password | password | min:8 | Sim |
| role | Papel | select | enum(UserRole) | Sim |

### 7. Formulário de Perfil (Atualização)

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| name | Nome | text | max:255 | Sim |
| email | Email | email | unique(ignore current) | Sim |

### 8. Formulário de Alteração de Password

**Campos:**
| Campo | Label | Tipo | Validação | Obrigatório |
|-------|-------|------|-----------|-------------|
| current_password | Password Atual | password | current_password | Sim |
| password | Nova Password | password | min:8, confirmed | Sim |
| password_confirmation | Confirmar Password | password | - | Sim |

---

## 🔐 Regras de Negócio e Permissões

### Níveis de Acesso

#### Admin (role = 1)
- Acesso total ao sistema
- Pode criar, editar e eliminar utilizadores
- Pode ver todas as faturas de todos os utilizadores
- Pode ver todas as folhas de quilometragem
- Acesso a todas as funcionalidades

#### Accountant (role = 2)
- Pode ver todas as faturas de todos os utilizadores
- Pode ver todas as folhas de quilometragem
- Não pode gerir utilizadores
- Acesso limitado às configurações

#### User (role = 3)
- Pode ver apenas as suas próprias faturas
- Pode ver apenas as suas folhas de quilometragem
- Pode criar novas faturas e folhas
- Acesso apenas ao seu perfil

### Validações de Negócio

#### Faturas
1. **ATCUD único**: Cada fatura deve ter um ATCUD único no sistema
2. **NIF de Despesa**: Para faturas de despesa, o NIF do adquirente deve corresponder ao NIF da empresa selecionada
3. **NIF de Venda**: Para faturas de venda, o NIF do emissor deve corresponder ao NIF da empresa selecionada
4. **Categoria Obrigatória**: Para despesas, a categoria é obrigatória
5. **Armazenamento**: Ficheiros são organizados por ano/mês

#### Quilometragem
1. **Matrícula**: Deve seguir o formato português (XX-XX-XX)
2. **Quilómetros**: Valor inteiro positivo até 999.999.999

#### Salários
1. **Funcionário**: Apenas utilizadores com role=USER podem ser selecionados
2. **Histórico**: O sistema busca o último salário do funcionário para pré-preencher

#### Utilizadores
1. **Email único**: Cada email deve ser único no sistema
2. **Admin protegido**: O utilizador com ID=1 não pode ser eliminado
3. **Auto-eliminação**: Um utilizador não pode eliminar-se a si próprio
4. **Verificação de email**: Email deve ser verificado para acesso completo

---

## 🛣️ Rotas e Endpoints

### Rotas Públicas
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | / | Redireciona para dashboard |
| GET | /maintenance | Página de manutenção |

### Rotas Autenticadas (require auth + verified)

#### Dashboard
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /dashboard | DashboardController@index | Dashboard principal |

#### Faturas
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /invoice | InvoiceController@index | Listar faturas |
| POST | /invoice/exists | InvoiceController@exists | Verificar se fatura existe |
| POST | /invoice | InvoiceController@store | Criar fatura |
| GET | /invoice/{invoice} | InvoiceController@show | Ver fatura |
| PATCH | /invoice/{invoice} | InvoiceController@update | Atualizar fatura |
| DELETE | /invoice/{invoice} | InvoiceController@destroy | Eliminar fatura |
| DELETE | /invoices/bulk | InvoiceController@destroyBulk | Eliminar múltiplas |
| GET | /invoice/{invoice}/download | InvoiceController@download | Download fatura |
| GET | /invoices/bulk | InvoiceController@downloadBulk | Download múltiplas |
| POST | /invoices/upload | InvoiceController@upload | Upload em massa |

#### Páginas de Documentos
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | /scanner | Página de scanner |
| GET | /uploads | Página de upload |
| GET | /invoiceform | Formulário manual |

#### Quilometragem
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /kilometers | KilometersController@index | Listar quilometragem |
| GET | /kilometer/create | KilometersController@create | Formulário criação |
| POST | /kilometer | KilometersController@store | Criar registo |
| DELETE | /kilometer/{kilometer} | KilometersController@destroy | Eliminar registo |
| DELETE | /kilometers/bulk | KilometersController@destroyBulk | Eliminar múltiplos |

#### Salários
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /salaries | SalariesController@index | Listar salários |
| GET | /salary/create | SalariesController@create | Formulário criação |
| GET | /salary | SalariesController@salary | Buscar último salário |
| POST | /salary | SalariesController@store | Criar salário |
| DELETE | /salary/{salary} | SalariesController@destroy | Eliminar salário |
| DELETE | /salaries/bulk | SalariesController@destroyBulk | Eliminar múltiplos |

#### Utilizadores (Admin only)
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /users | UserController@index | Listar utilizadores |
| GET | /user | UserController@create | Formulário criação |
| GET | /user/{user} | UserController@show | Ver utilizador |
| POST | /user | UserController@store | Criar utilizador |
| PATCH | /user/{user} | UserController@update | Atualizar utilizador |
| DELETE | /user/{user} | UserController@destroy | Eliminar utilizador |
| DELETE | /users/bulk | UserController@destroyBulk | Eliminar múltiplos |

#### Empresa
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /company | - | Página de empresa |
| POST | /company | CompanyController@store | Criar empresa |
| PATCH | /company | CompanyController@update | Atualizar empresa |

#### Configurações
| Método | Rota | Controller | Descrição |
|--------|------|------------|-----------|
| GET | /settings | - | Redireciona para profile |
| GET | /settings/profile | ProfileController@edit | Editar perfil |
| PATCH | /settings/profile | ProfileController@update | Atualizar perfil |
| DELETE | /settings/profile | ProfileController@destroy | Eliminar conta |
| GET | /settings/password | PasswordController@edit | Alterar password |
| PUT | /settings/password | PasswordController@update | Atualizar password |
| GET | /settings/appearance | - | Aparência |

#### Outras Despesas
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | /otherexpenses | Menu outras despesas |

---

## 📋 Funcionalidades Especiais

### 1. Sistema de Mensagens Flash
Todas as operações retornam mensagens flash com a estrutura:
```php
[
    'success' => boolean,
    'text' => 'Mensagem para o utilizador'
]
```

### 2. Upload de Faturas
- **Formato Individual**: PNG através do scanner ou formulário manual
- **Formato em Massa**: PDFs com extração automática de dados
- **Organização**: Ficheiros organizados em storage/invoices/{ano}/{mês}/

### 3. Download de Documentos
- **Individual**: Gera ZIP com CSV de metadados + ficheiro original
- **Em Massa**: Gera ZIP com todos os ficheiros selecionados + CSV consolidado

### 4. Pré-preenchimento de Salários
Sistema busca automaticamente o último salário registado do funcionário selecionado

### 5. Verificação de Email
Sistema implementa verificação de email obrigatória para acesso completo

---

## 🔄 Relacionamentos

### User
- **hasMany** → Invoice (created_by)
- **hasMany** → Kilometer (created_by)
- **hasMany** → Salary (created_by)
- **hasMany** → Salary (employee_id)

### Company
- **hasMany** → Invoice
- **hasMany** → Kilometer
- **hasMany** → Salary

### Invoice
- **belongsTo** → User (created_by)
- **belongsTo** → Company
- **belongsTo** → InvoiceCategory (category_id)

### InvoiceCategory
- **hasMany** → Invoice

### Kilometer
- **belongsTo** → User (created_by)
- **belongsTo** → Company

### Salary
- **belongsTo** → User (employee_id)
- **belongsTo** → User (created_by)
- **belongsTo** → Company

---

## 🎨 Interfaces de Utilizador (Blade)

### Páginas Necessárias

1. **Dashboard** (`dashboard.blade.php`)
   - Estatísticas gerais
   - Gráficos de despesas/vendas
   - Resumos por categoria

2. **Faturas**
   - `invoices/index.blade.php` - Listagem com filtros e ações em massa
   - `invoices/show.blade.php` - Visualização detalhada
   - `invoices/scanner.blade.php` - Interface de scanner
   - `invoices/upload.blade.php` - Upload em massa
   - `invoices/manual.blade.php` - Formulário manual

3. **Quilometragem**
   - `kilometers/index.blade.php` - Listagem
   - `kilometers/create.blade.php` - Formulário

4. **Salários**
   - `salaries/index.blade.php` - Listagem
   - `salaries/create.blade.php` - Formulário com AJAX para pré-preenchimento

5. **Utilizadores** (Admin)
   - `users/index.blade.php` - Listagem
   - `users/create.blade.php` - Formulário criação
   - `users/show.blade.php` - Detalhes do utilizador

6. **Empresa**
   - `company/edit.blade.php` - Formulário de empresa

7. **Configurações**
   - `settings/profile.blade.php` - Perfil
   - `settings/password.blade.php` - Alterar password
   - `settings/appearance.blade.php` - Aparência

8. **Autenticação**
   - Login, Register, Forgot Password, Verify Email

### Componentes Blade Recomendados

1. **Layout Principal** (`layouts/app.blade.php`)
   - Navbar com menu de navegação
   - Sidebar com links por módulo
   - Footer

2. **Tabelas** (`components/data-table.blade.php`)
   - Cabeçalhos ordenáveis
   - Checkboxes para seleção múltipla
   - Ações em massa
   - Paginação

3. **Formulários** (`components/form-*.blade.php`)
   - Input groups
   - Select com pesquisa
   - File upload
   - Validação client-side

4. **Mensagens** (`components/flash-message.blade.php`)
   - Alertas de sucesso/erro
   - Auto-dismiss

5. **Modais** (`components/modal.blade.php`)
   - Confirmação de eliminação
   - Visualização rápida

---

## 📦 Dependências e Configurações

### Pacotes Laravel Necessários
- Laravel Framework 11.x
- Laravel Breeze (autenticação)
- Intervention/Image (manipulação de imagens)
- Maatwebsite/Excel (opcional para exportação)

### Configurações Storage
```php
// config/filesystems.php
'invoices' => [
    'driver' => 'local',
    'root' => storage_path('app/invoices'),
    'url' => env('APP_URL').'/invoices',
    'visibility' => 'private',
],
```

### Middleware Necessários
- `auth` - Autenticação
- `verified` - Verificação de email
- Middleware customizado para verificar role de admin

---

## 🚀 Passos de Implementação

1. **Configuração Base**
   - Instalar Laravel 11
   - Configurar base de dados
   - Instalar Laravel Breeze com Blade

2. **Criar Migrations**
   - Executar migrations na ordem correta
   - Popular tabelas com seeders de teste

3. **Implementar Models**
   - Criar models com fillable e casts
   - Definir relacionamentos
   - Criar Enums

4. **Desenvolver Controllers**
   - Implementar lógica de negócio
   - Adicionar validações
   - Configurar mensagens flash

5. **Criar Views Blade**
   - Layout principal
   - Páginas individuais
   - Componentes reutilizáveis

6. **Configurar Rotas**
   - Rotas web com middleware
   - Agrupamentos por módulo

7. **Testes**
   - Testes de validação
   - Testes de permissões
   - Testes de upload/download

---

## 📝 Notas Importantes

1. **Idioma**: Sistema em português de Portugal
2. **Moeda**: Euros (€)
3. **Formato de Data**: DD/MM/YYYY
4. **Formato de Matrícula**: XX-XX-XX (português)
5. **IVA**: Sistema preparado para período mensal ou trimestral
6. **ATCUD**: Código único obrigatório para faturas portuguesas
7. **Categorias**: Adaptadas à legislação fiscal portuguesa

---

## 🔒 Segurança

1. **Autenticação**: Laravel Breeze com verificação de email
2. **Autorização**: Sistema de roles (Admin, Accountant, User)
3. **Validação**: Server-side em todos os formulários
4. **Storage**: Ficheiros privados não acessíveis publicamente
5. **CSRF**: Proteção em todos os formulários
6. **SQL Injection**: Uso de Eloquent ORM e query builder
7. **XSS**: Escape automático do Blade

---

Esta documentação contém todos os requisitos necessários para recriar o sistema OBBA usando Laravel com Blade templates, sem necessidade de Vue.js.