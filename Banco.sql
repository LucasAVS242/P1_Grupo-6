CREATE DATABASE pi;

USE pi;

-- Criando as tabelas
CREATE TABLE tb_cursos(
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nome_curso VARCHAR(60),
    sigla VARCHAR(5)
);

CREATE TABLE tb_disciplinas(
    id_disciplina INT AUTO_INCREMENT PRIMARY KEY,
    nome_disciplina VARCHAR(100),
    id_curso INT,
    qtde_aulas INT,
    FOREIGN KEY (id_curso) REFERENCES tb_cursos(id_curso)
);

CREATE TABLE tb_usuarios(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60),
    matricula VARCHAR(20),
    usuario VARCHAR(50),
    senha VARCHAR(50),  
    tipo_usuario VARCHAR(50)
);

CREATE TABLE tb_usuarioDisciplina(
    id_usuarioDisciplina INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_disciplina INT,
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario),
    FOREIGN KEY (id_disciplina) REFERENCES tb_disciplinas(id_disciplina)
);

CREATE TABLE tb_formsJustificativa(
    id_formJustificativa INT AUTO_INCREMENT PRIMARY KEY,
    data_envio DATE,
    motivo VARCHAR(255),
    nome_arquivo VARCHAR(100),
    status VARCHAR(30),
    observacoes_coordenador VARCHAR(255),
    id_usuario INT,
    id_curso INT,
    FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario),
    FOREIGN KEY (id_curso) REFERENCES tb_cursos(id_curso)
);

CREATE TABLE tb_aulasNaoMinistradas(
    id_aulaNaoMinistrada INT AUTO_INCREMENT PRIMARY KEY,
    data DATE,
    quantidade_aulas INT,
    id_disciplina INT,
    id_formJustificativa INT,
    FOREIGN KEY (id_disciplina) REFERENCES tb_disciplinas(id_disciplina),
    FOREIGN KEY (id_formJustificativa) REFERENCES tb_formsJustificativa(id_formJustificativa)
);

CREATE TABLE tb_formsReposicao(
    id_formReposicao INT AUTO_INCREMENT PRIMARY KEY,
    turno VARCHAR(5),
    motivo_reposicao VARCHAR(15),
    id_formJustificativa INT,
    FOREIGN KEY (id_formJustificativa) REFERENCES tb_formsJustificativa(id_formJustificativa)
);


CREATE TABLE tb_aulasReposicao(
    id_aulasReposicao INT AUTO_INCREMENT PRIMARY KEY,
    data DATE,
    horario_inicio TIME,
    horario_final TIME,
    id_disciplina INT,
    id_formReposicao INT,
    FOREIGN KEY (id_disciplina) REFERENCES tb_disciplinas(id_disciplina),
    FOREIGN KEY (id_formReposicao) REFERENCES tb_formsReposicao(id_formReposicao)
);

-- Inserindo dados
-- Cursos
INSERT INTO tb_cursos (nome_curso, sigla) VALUES 
('Desenvolvimento de Software Multiplataforma', 'DSM'), ('Gestão de Tecnologia da Informação', 'GTI'), ('Gestão da Produção Industrial', 'GPI'), ('Gestão Empresarial', 'GE');

-- Disciplinas DSM
INSERT INTO tb_disciplinas(nome_disciplina, id_curso, qtde_aulas) VALUES 
-- 1º Semestre
('Sistemas Operacionais e Redes de Computadores', 1, 4), ('Desenvolvimento Web I', 1, 4), ('Algoritmo e Lógica de Programação', 1, 4), ('Design Digital', 1, 4), ('Modelagem de Banco de Dados', 1, 4), ('Engenharia de Software I', 1, 4),
-- 2º Semestre
('Banco de Dados Relacional', 1, 4), ('Engenharia de Software II', 1, 4), ('Matemática Para Computação', 1, 4), ('Desenvolvimento Web II', 1, 4), ('Técnicas de Programação', 1, 4), ('Estrutura de Dados', 1, 4), 
-- 3º Semestre
('Álgebra Linear', 1, 4), ('Desenvolvimento Web III', 1, 4), ('Banco de Dados Não Relacional', 1, 4), ('Gestão Ágil de Projetos de Software', 1, 4), ('Técnicas de Programação II', 1, 4), ('Interação Humano Computador', 1, 2), ('Inglês I', 1, 2),
-- 4º Semestre 
('Internet das Coisas e Aplicações', 1, 4), ('Estatística Aplicada', 1, 4), ('Inglês II', 1, 2), ('Experiência do Usuário', 1, 2), ('Laboratório de Desenvolvimento Web', 1, 4), ('Programação Para dispositivos Móveis I', 1, 4), ('Integração e Entrega Contínua', 1, 4),
-- 5º Semestre
('Programação para Dispositivos Móveis II', 1, 4), ('Aprendizagem de Máquina', 1, 4), ('Computação em Nuvem I', 1, 4), ('Laboratório de Desenvolvimento para Dispositivos Móveis', 1, 4), ('Fundamentos da Redação Técnica', 1, 2), ('Inglês III', 1, 2), ('Segurança no Desenvolvimento das Aplicações', 1, 4), 
-- 6º Semestre
('Processamento de Linguagem Natural', 1, 4), ('Qualidade de Testes de Software', 1, 4), ('Mineração de Dados', 1, 4), ('Laboratório de Desenvolvimento Multiplataforma', 1, 4), ('Inglês IV', 1, 2), ('Ética Profissional e Patente', 1, 2), ('Computação em Nuvem II', 1, 4);

-- Cadastrando usuarios Usuarios
INSERT INTO tb_usuarios(usuario, senha, tipo_usuario, nome, matricula) VALUES
    ('Junior@gmail.com', 'Senha1', 'PROFESSOR', 'José Gonçalves Pinto Junior', 1111), ('Ana@gmail.com', 'Senha2', 'PROFESSOR', 'Ana Célia Ribeiro Bizigato Portes', 1112), ('Rafael@gmail.com', 'Senha3', 'PROFESSOR', 'Rafael Martins Gomes', 1113), ('Reginaldo@gmail.com', 'Senha4', 'PROFESSOR', 'Reginaldo Donizeti Candido', 1114), ('Maromo@gmail.com', 'Senha5', 'PROFESSOR', 'Marcos Roberto de Moraes', 1115), ('Thiago@gmail.com', 'Senha6', 'PROFESSOR', 'Thiago Salhab Alves', 1116), ('Marcia@gmail.com', 'Senha1', 'COORDENADOR', 'Marcia Regina Reggiolli', 1117);

-- Cadastrando as materias lecionadas pelos professores
-- Coloquei apenas os professores que são aula na nossa sala
INSERT INTO tb_usuarioDisciplina(id_usuario, id_disciplina) VALUES
    (1, 3), (1, 7),(1, 14),(1, 17),(1, 24),(1, 33),
    (2, 6), (2, 8), (2, 16), 
    (3, 9),
    (4, 10), (4, 20),(4, 29),(4, 35),
    (5, 11),
    (6, 12), (6, 18);

