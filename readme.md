# TimeTracker

Sistema pessoal de rastreamento de tempo. Registra automaticamente o uso de aplicativos no Windows, atividade em navegadores (aba/URL) e permite lançamento manual de tempo. Tudo consolidado em um dashboard de relatórios.

> Nome provisório. Substituir por busca/replace se decidir por outro (candidatos: TimeFlow, BiuTrack, PulseTime).

**Status: v1 funcional e testada ponta a ponta** (API, agente Windows, extensão de navegador, formulário manual e dashboard). Veja [Status atual e próximos passos](#status-atual-e-próximos-passos) para o que falta polir.

## Objetivo

Ter visibilidade real de onde o tempo é gasto no dia a dia (trabalho, estudo, projetos pessoais), sem depender de anotação manual constante. Uso pessoal, single-user, sem necessidade de multi-tenant.

## Escopo (v1)

- [x] Captura de app ativo no Windows
- [x] Captura de aba/URL em navegador (Chrome/Edge)
- [x] Lançamento manual de tempo
- [x] API REST para ingestão e consulta
- [x] Dashboard com relatórios (tempo por app/domínio/projeto/dia)
- [ ] Fora do escopo da v1: multi-usuário, mobile, sincronização multi-máquina

## Arquitetura

```
┌─────────────────┐   ┌──────────────────────┐   ┌──────────────────┐
│ Agente Windows   │   │ Extensão navegador    │   │ Entrada manual    │
│ (Python)         │   │ (Manifest V3)         │   │ (form web)        │
│ Monitora app     │   │ Captura aba + URL     │   │                   │
│ ativo em foco    │   │                       │   │                   │
└────────┬─────────┘   └───────────┬───────────┘   └─────────┬────────┘
         │                         │                         │
         └────────────┬────────────┴────────────┬────────────┘
                       ▼                         ▼
              ┌─────────────────────────────────────┐
              │        API REST (PHP + CodeIgniter)  │
              │  POST /activities   POST /manual     │
              │  GET  /report                        │
              └────────────────┬──────────────────────┘
                                ▼
                        ┌───────────────┐
                        │     MySQL     │
                        └───────┬───────┘
                                ▼
                        ┌───────────────┐
                        │   Dashboard   │
                        │ (CodeIgniter  │
                        │  + Chart.js)  │
                        └───────────────┘
```

Cada fonte de dados é independente — o agente Windows e a extensão de navegador não se comunicam entre si, ambos falam direto com a API.

## Stack

| Camada | Tecnologia | Motivo |
|---|---|---|
| Agente desktop | Python 3.11+ (`pywin32`, `psutil`, `pystray`) | Único jeito real de capturar janela ativa no Windows — **só roda no Windows** |
| Extensão navegador | JavaScript, Manifest V3 | API `chrome.tabs` só existe em extensão |
| Backend/API | PHP 8.2+ / CodeIgniter 4 | Stack de produção já dominada |
| Banco | MySQL 8 | Já em uso no ambiente atual |
| Dashboard | CodeIgniter views + Tailwind CSS (CDN) + Chart.js | Sem necessidade de SPA/build step para uso pessoal |

## Estrutura de pastas

```
timetracker/
├── agent/                       # Agente Python (Windows)
│   ├── main.py                  # loop principal: sessão, idle, envio pra API, ícone na bandeja
│   ├── idle_detector.py         # detecção de inatividade (Win32 GetLastInputInfo)
│   ├── window_tracker.py        # captura de app + título da janela ativa
│   ├── requirements.txt
│   └── .env.example
├── extension/                   # Extensão de navegador (Manifest V3)
│   ├── manifest.json
│   ├── background.js            # service worker: sessão por aba/domínio, envio pra API
│   ├── popup.html / popup.js    # configuração (URL da API, API Key, capturar URL completa)
├── api/                         # Backend + Dashboard (CodeIgniter 4)
│   ├── app/Controllers/         # Api/ (endpoints JSON) + DashboardController + ManualEntryController
│   ├── app/Models/               # ActivityModel, ProjetoModel
│   ├── app/Services/             # ReportService (agregações compartilhadas por API e dashboard)
│   ├── app/Views/                 # dashboard.php, manual_entry.php, partials/nav.php
│   ├── app/Config/                # Routes.php, Filters.php (apikey, csrf), Security.php
│   └── env                       # template de variáveis de ambiente
├── docs/
│   └── schema.sql                # schema completo do MySQL
└── readme.md
```

## Pré-requisitos

Para rodar o projeto completo em outra máquina, você precisa instalar:

| Ferramenta | Versão | Para quê | Obrigatório? |
|---|---|---|---|
| [PHP](https://www.php.net/) | 8.2+ | Rodar a API/Dashboard | Sim |
| [Composer](https://getcomposer.org/) | 2.x | Instalar dependências do CodeIgniter 4 | Sim |
| [MySQL](https://dev.mysql.com/downloads/) | 8.x | Banco de dados | Sim |
| [Python](https://www.python.org/) | 3.11+ | Rodar o agente Windows | Só se for usar o agente |
| Navegador Chromium (Chrome/Edge) | recente | Rodar a extensão | Só se for usar a extensão |
| Git | qualquer | Clonar o repositório | Sim |

**Windows only para o agente**: `pywin32` só funciona no Windows — o agente de captura de app ativo não roda em Linux/Mac. A API, o banco e a extensão de navegador funcionam em qualquer SO.

Em ambiente Windows, o jeito mais rápido de ter PHP + Composer + MySQL prontos de uma vez é instalar o **[Laragon](https://laragon.org/)** (foi o usado no desenvolvimento) — ele já vem com tudo integrado e evita configurar cada ferramenta separadamente. Alternativas: XAMPP, WAMP, ou instalar cada peça isoladamente.

## Como rodar — passo a passo

### 1. Banco de dados

Crie o banco e aplique o schema:

```sql
CREATE DATABASE timetracker CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

```bash
mysql -u root -p timetracker < docs/schema.sql
```

### 2. API + Dashboard

```bash
cd api
composer install
cp env .env
```

Edite `api/.env` e preencha (veja a tabela de variáveis abaixo):
- `CI_ENVIRONMENT = development`
- `database.default.hostname`, `database.default.database`, `database.default.username`, `database.default.password`
- `API_KEY` — invente uma chave qualquer (ex.: `openssl rand -hex 16` ou qualquer gerador de senha)

Suba o servidor:

```bash
php spark serve
```

Acesse `http://localhost:8080/` — deve abrir o dashboard (vazio, sem dados ainda).

### 3. Agente Windows (opcional, só se for rodar no Windows)

```bash
cd agent
python -m venv .venv
.venv\Scripts\pip install -r requirements.txt
copy .env.example .env
```

Edite `agent/.env` com a mesma `API_KEY` configurada na API e a URL onde a API está rodando (`API_BASE_URL`).

```bash
.venv\Scripts\python main.py
```

Aparece um ícone na bandeja do sistema. Ele passa a monitorar o app em foco e enviar sessões fechadas pra API.

### 4. Extensão de navegador (opcional)

1. Abra `chrome://extensions` (ou `edge://extensions`)
2. Ative "Modo do desenvolvedor"
3. "Carregar sem compactação" → selecione a pasta `extension/`
4. Clique no ícone da extensão → preencha **URL da API** e **API Key** (mesma da API) → Salvar

### 5. Lançamento manual e Dashboard

Nenhuma configuração extra — acesse `http://localhost:8080/lancamento` pra lançar tempo manualmente, e `http://localhost:8080/` pra ver os relatórios.

## Variáveis de ambiente

### `api/.env` (copiado de `api/env`)

| Variável | Exemplo | Descrição |
|---|---|---|
| `CI_ENVIRONMENT` | `development` | Ambiente do CodeIgniter (`development` mostra erros detalhados e a debug toolbar) |
| `database.default.hostname` | `localhost` | Host do MySQL |
| `database.default.database` | `timetracker` | Nome do banco |
| `database.default.username` | `root` | Usuário do MySQL |
| `database.default.password` | *(vazio ou sua senha)* | Senha do MySQL |
| `API_KEY` | *(gere uma chave aleatória)* | Chave exigida no header `X-API-Key` para todas as rotas `/api/*` |

### `agent/.env` (copiado de `agent/.env.example`)

| Variável | Padrão | Descrição |
|---|---|---|
| `API_BASE_URL` | `http://127.0.0.1:8080` | Onde a API está rodando |
| `API_KEY` | — | Mesma chave configurada em `api/.env` |
| `POLL_INTERVAL_SECONDS` | `2` | Intervalo de checagem da janela ativa |
| `IDLE_THRESHOLD_SECONDS` | `300` | Tempo sem input pra considerar ocioso (pausa a contagem) |
| `FLUSH_INTERVAL_SECONDS` | `30` | Intervalo de envio das sessões fechadas pra API |

### Extensão de navegador

Não usa arquivo `.env` — configuração é feita pelo popup da extensão (ícone → formulário) e fica salva no `chrome.storage.local` do navegador: **URL da API**, **API Key**, e o toggle **capturar URL completa** (padrão: só domínio).

## Schema do banco (MySQL)

Ver [`docs/schema.sql`](docs/schema.sql). Resumo:

```sql
CREATE TABLE projetos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  cor VARCHAR(7) DEFAULT '#888888'
) ENGINE=InnoDB;

CREATE TABLE activities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  origem ENUM('app','navegador','manual') NOT NULL,
  app_ou_dominio VARCHAR(255) NOT NULL,
  titulo VARCHAR(500) NULL,
  projeto_id INT UNSIGNED NULL,
  inicio DATETIME NOT NULL,
  fim DATETIME NOT NULL,
  duracao_seg INT UNSIGNED GENERATED ALWAYS AS (TIMESTAMPDIFF(SECOND, inicio, fim)) STORED,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (projeto_id) REFERENCES projetos(id),
  INDEX idx_inicio (inicio),
  INDEX idx_projeto_periodo (projeto_id, inicio)
) ENGINE=InnoDB;
```

## Endpoints da API

Todas exigem o header `X-API-Key` (autenticação simples, uso pessoal, sem OAuth).

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/activities` | Recebe lote de sessões do agente/extensão (`{"activities": [...]}`) |
| `POST` | `/api/manual` | Cria um lançamento manual (`origem` sempre forçado pra `"manual"`) |
| `GET` | `/api/report?de=&ate=&projeto_id=` | Agregações (total, por app, por projeto, por dia) pro dashboard |
| `GET` | `/api/projetos` | Lista projetos/tags |
| `POST` | `/api/projetos` | Cria novo projeto/tag |

Rotas web (sessão de navegador + CSRF, não usam `X-API-Key`):

| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/` | Dashboard |
| `GET`/`POST` | `/lancamento` | Formulário de lançamento manual |

## Regras de negócio importantes

- **Agregação de sessão**: nunca gravar uma linha por polling. O agente/extensão só envia um registro quando o app/aba muda (início e fim da sessão anterior).
- **Idle detection**: se não houver input de mouse/teclado por um período configurável (padrão: 5 min), pausar a contagem automaticamente.
- **Domínio vs URL completa**: por padrão, a extensão registra só o domínio (`github.com`). URL completa é opcional e configurável, para não virar histórico de navegação.
- **Duração calculada**: sempre via `TIMESTAMPDIFF` (coluna gerada), nunca recalculada em query de relatório.

## Status atual e próximos passos

O que já está implementado e testado ponta a ponta (API ↔ MySQL ↔ agente/extensão/dashboard):

- Schema MySQL, os 5 endpoints da API, agente Windows (captura + idle detection), extensão de navegador (Manifest V3), formulário de lançamento manual, dashboard com Chart.js.

O que ainda pode/deve ser melhorado por quem continuar o projeto:

- **Frontend**: o dashboard e o formulário usam Tailwind CSS via CDN (sem build step) — funcional e já com tema escuro, mas dá pra evoluir bastante: componentização, mais gráficos/filtros, responsividade mobile, talvez migrar pra um build real (Vite) se a UI crescer.
- **Testes automatizados**: não há testes (PHPUnit já vem configurado no CodeIgniter mas nenhum teste foi escrito ainda).
- **CI/CD**: nenhum pipeline configurado.
- **Gestão de projetos pela UI**: `POST/GET /api/projetos` existe na API, mas não há tela no dashboard pra criar/editar projetos — hoje só dá pra criar via API diretamente.
- **Auto-start do agente**: hoje precisa rodar `python main.py` manualmente a cada boot do Windows; falta um atalho na pasta de Inicialização ou um instalador/serviço.
- **Segurança da API key**: autenticação é uma chave estática simples — adequado pro uso pessoal atual, mas não escala pra múltiplos usuários/dispositivos sem retrabalho.
- **Sincronização multi-máquina**: fora do escopo da v1 (declarado desde o início), mas seria o próximo passo natural se o uso crescer.

## Convenções de código

- PHP: PSR-12, seguindo padrões já usados nos outros projetos (PRORADIS/TELERADIS)
- Commits em português, formato `tipo: descrição` (ex: `feat: adiciona endpoint de relatório`)
- Sem lógica de negócio em Controller — sempre delegar para Model/Service

## Notas para o Claude Code

- Este é um projeto pessoal, single-user — não implementar autenticação multi-usuário, roles ou permissões complexas.
- Priorizar simplicidade e manutenibilidade sobre abstração excessiva.
- Ao criar o agente Python, validar se `pywin32` está instalado antes de assumir a API disponível.
- Ao gerar SQL, sempre considerar índices para os filtros de relatório (`inicio`, `projeto_id`).
