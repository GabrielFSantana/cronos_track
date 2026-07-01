-- TimeTracker - schema MySQL
-- Gerado a partir do README.md (seção "Schema do banco")

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
