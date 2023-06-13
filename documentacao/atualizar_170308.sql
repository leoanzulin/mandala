DELETE FROM authitemchild;
DELETE FROM authitem WHERE type != 2;

COPY authitem (name, type, description, bizrule, data) FROM stdin;
Site.*	1	\N	\N	N;
Pages.View	0	\N	\N	N;
Inscricao.Index	0	\N	\N	N;
Inscricao.Sucesso	0	\N	\N	N;
Inscricao.Documentos	0	\N	\N	N;
Inscricao.DocumentosSucesso	0	\N	\N	N;
Inscricao.InformacoesComplementares	0	\N	\N	N;
Aluno.*	1	\N	\N	N;
Aluno.Index	0	\N	\N	N;
Aluno.Perfil	0	\N	\N	N;
Aluno.EditarPerfil	0	\N	\N	N;
Aluno.TrocarSenha	0	\N	\N	N;
TrocarSenha.*	1	\N	\N	N;
Oferta.PorPeriodo	0	\N	\N	N;
Habilitacao.Get	0	\N	\N	N;
\.

COPY authitemchild (parent, child) FROM stdin;
Guest	Site.*
Guest	TrocarSenha.*
Guest	Inscricao.Index
Guest	Inscricao.Sucesso
Guest	Pages.View
Guest	Inscricao.Documentos
Guest	Inscricao.DocumentosSucesso
InscritoComInformacoesFaltantes	Inscricao.InformacoesComplementares
Inscrito	Aluno.Perfil
Inscrito	Aluno.EditarPerfil
Inscrito	Aluno.Index
Inscrito	Inscricao.InformacoesComplementares
Inscrito	Aluno.TrocarSenha
Aluno	Oferta.PorPeriodo
Aluno	Habilitacao.Get
Aluno	Aluno.*
\.
