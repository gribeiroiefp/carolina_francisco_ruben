CREATE TABLE autores(
id INT AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(40) NOT NULL,
data_nascimento DATE NOT NULL,
nacionalidade VARCHAR(40) NOT NULL,
foto VARCHAR(255) NOT NULL
);
 
CREATE TABLE livros(
id INT AUTO_INCREMENT PRIMARY KEY,
titulo VARCHAR(40)NOT NULL,
ano YEAR NOT NULL,
capa VARCHAR (255) NOT NULL
);
 
CREATE TABLE autores_livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_livro INT,
    id_autor INT,    
    FOREIGN KEY (id_autor) REFERENCES autores(id) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livros(id) ON DELETE CASCADE
 
);

-- Inserir livros
INSERT INTO livros (titulo, ano, capa) VALUES
('Memorial do Convento', 1982,'uploads/capas/memorial_do_convento.jpg'),
('Ensaio sobre a Cegueira', 1995,'uploads/capas/ensaio_sobre_a_cegueira.jpg'),
('The Talisman', 1984,'uploads/capas/the_talisman.jpg'),
('The Shinning', 1977,'uploads/capas/the_shinning.jpg'),
('Ghost Story', 1979,'uploads/capas/ghost_story.jpg'),
('Ensaio sobre a lucidez', 2004,'uploads/capas/ensaio_sobre_a_lucidez.jpg'),
('O Crime do Padre Amaro', 1875,'uploads/capas/crime_do_padre_amaro.jpg'),
('Os Maias', 1888,'uploads/capas/os_maias.jpg');


-- Inserir autores
INSERT INTO autores (nome, data_nascimento, nacionalidade, foto) VALUES
('José Saramago', '1922-11-16', 'Portugal', 'uploads/pictures/jose_saramago.jpg'), -- 3 livros
('Peter Straub', '1943-03-02', 'United States', 'uploads/pictures/peter_straub.jpg'), -- 2 livros
('Stephen King', '1947-09-21', 'United States', 'uploads/pictures/stephen_king.jpg'), -- 2 livros
('J. R. R. Tolkien', '1892-01-03', 'United Kingdom', 'uploads/pictures/j_r_r_tolkien.jpg'), -- 0 livros
('Eça de Queirós', '1845-11-25', 'Portugal', 'uploads/pictures/Eça_de_Queiros.jpg'); -- 2 livros


 
-- Inserir livro_autor

INSERT INTO autores_livros(id_livro, id_autor) VALUES 
(1, 1), -- (Memorial do Convento, José Saramago)
(2, 1), -- (Ensaio sobre a Cegueira, José Saramago)
(3, 2), -- (The Talisman, Peter Straub)
(3, 3), -- (The Talisman, Stephen King)
(4, 3), -- (The Shinning, Stephen King)
(5, 2), -- (Ghost Story, Peter Straub)
(6, 1), -- (Ensaio sobre a lucidez, José Saramago)
(7, 1), -- (O Crime do Padre Amaro, eça de queiros)
(8, 1); -- (Os Maias, eça de queiros )