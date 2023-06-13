ALTER TABLE inscricao DROP COLUMN IF EXISTS candidato_bolsa;
ALTER TABLE inscricao DROP COLUMN IF EXISTS siape;
ALTER TABLE inscricao DROP COLUMN IF EXISTS bolsa_parcial_ou_total;

DELETE FROM authitem;

INSERT INTO authitem(name, type, data) VALUES('Admin', 2, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Guest', 2, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Site.*', 1, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Inscricao.*', 1, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Inscricao.Index', 1, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Inscricao.Sucesso', 1, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Pages.*', 1, 'N;');
INSERT INTO authitem(name, type, data) VALUES('Pages.View', 0, 'N;');

DELETE FROM authitemchild;

INSERT INTO authitemchild(parent, child) VALUES('Guest', 'Site.*');
INSERT INTO authitemchild(parent, child) VALUES('Guest', 'Inscricao.Index');
INSERT INTO authitemchild(parent, child) VALUES('Guest', 'Inscricao.Sucesso');
INSERT INTO authitemchild(parent, child) VALUES('Guest', 'Pages.View');


