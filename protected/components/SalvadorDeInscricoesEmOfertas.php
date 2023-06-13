<?php

/**
 * Componente responsável pelo salvamento de inscrições de alunos em ofertas.
 * Chamada tanto em AlunoController (quando um aluno faz sua inscrição em
 * ofertas) quanto em AdminController (quando a coordenação modifica as
 * inscrições de um aluno).
 */
class SalvadorDeInscricoesEmOfertas
{
    private $selecaoDeComponentesParaCertificados = false;
    private $inscricao;
    private $habilitacoes;
    private $idsDasOfertasEHabilitacoesASeremInscritas;
    private $validador;
    private $mensagensDeErro = [];

    public function ehSelecaoDeComponentesParaCertificados()
    {
        $this->selecaoDeComponentesParaCertificados = true;
    }

    public function __construct(Inscricao $inscricao, $idsDasOfertasEHabilitacoesASeremInscritas)
    {
        $this->inscricao = $inscricao;
        $this->habilitacoes = $inscricao->recuperarHabilitacoes();
        $this->idsDasOfertasEHabilitacoesASeremInscritas = $idsDasOfertasEHabilitacoesASeremInscritas;
        $this->validador = new ValidadorDeInscricoesEmOfertas($inscricao, $this->habilitacoes, $idsDasOfertasEHabilitacoesASeremInscritas);
        $this->verificarParametros();
    }

    private function verificarParametros()
    {
        if (empty($this->inscricao)) {
            throw new Exception('Inscrição não foi atribuída.');
        }
        if (empty($this->idsDasOfertasEHabilitacoesASeremInscritas)) {
            throw new Exception('IDS das inscrições a serem salvas não foram atribuídos.');
        }
    }

    public function salvar()
    {
        if (!$this->validador->validar()) {
            $this->mensagensDeErro = $this->validador->recuperarMensagensDeErro();
            return false;
        }

        return $this->salvarInscricoes();
    }

    private function salvarInscricoes()
    {
        $informacoesInscricoesOfertas = $this->recuperarInformacoesDasInscricoesEmOfertas();
        $inscricoesHabilitacoesOfertasAntesDaInscricao = $this->recuperarInscricoesHabilitacoesEmOfertas();
        $inscricoesHabilitacoesOfertasCertificadosAntesDaInscricao = $this->recuperarInscricoesHabilitacoesEmOfertasParaCertificados();

        $transaction = Yii::app()->db->beginTransaction();

        try {
            $sql = "DELETE FROM habilitacao_inscricao_oferta_certificados WHERE inscricao_id = {$this->inscricao->id}";
            Yii::app()->db->createCommand($sql)->execute();
            if (!$this->selecaoDeComponentesParaCertificados) {
                $sql = "DELETE FROM habilitacao_inscricao_oferta WHERE inscricao_id = {$this->inscricao->id}";
                Yii::app()->db->createCommand($sql)->execute();
                $sql = "DELETE FROM inscricao_oferta WHERE inscricao_id = {$this->inscricao->id}";
                Yii::app()->db->createCommand($sql)->execute();
            }

            $inscricaoOfertas = [];
            $inscricaoOfertasHabilitacoes = [];
            foreach ($this->idsDasOfertasEHabilitacoesASeremInscritas as $ofertaId => $habilitacoes) {
                $informacoesDestaInscricaoEmOferta = $informacoesInscricoesOfertas[$ofertaId] ?? null;

                $values = [
                    $this->inscricao->id,
                    $ofertaId,
                    $informacoesDestaInscricaoEmOferta ? "'{$informacoesDestaInscricaoEmOferta['status']}'" : 'NULL',
                    $informacoesDestaInscricaoEmOferta['nota_virtual'] ?? 'NULL',
                    $informacoesDestaInscricaoEmOferta['nota_presencial'] ?? 'NULL',
                    $informacoesDestaInscricaoEmOferta['media'] ?? 'NULL',
                    $informacoesDestaInscricaoEmOferta['frequencia'] ?? 'NULL',
                    $this->tratarComoBooleano($informacoesDestaInscricaoEmOferta['confirmada']),
                ];
                $inscricaoOfertas[] = '(' . implode(',', $values) . ')';

                if ($this->inscricao->ehAlunoDeEspecializacao()) {
                    foreach ($habilitacoes as $habilitacao) {
                        $inscricaoOfertasHabilitacoes[] = "({$habilitacao}, {$this->inscricao->id}, {$ofertaId})";
                    }
                }
            }
            $inscricaoOfertasHabilitacoesCertificados = [];
            foreach ($inscricoesHabilitacoesOfertasCertificadosAntesDaInscricao as $habilitacaoOferta) {
                [ $habilitacaoId, $ofertaId ] = explode('_', $habilitacaoOferta);
                // Filtra as inscrições em ofertas que não existirão mais
                if (!array_key_exists($ofertaId, $this->idsDasOfertasEHabilitacoesASeremInscritas)) continue;
                $inscricaoOfertasHabilitacoesCertificados[] = "({$habilitacaoId}, {$this->inscricao->id}, {$ofertaId})";
            }

            if (!$this->selecaoDeComponentesParaCertificados) {
                if (!empty($inscricaoOfertas)) {
                    $sql = 'INSERT INTO inscricao_oferta(inscricao_id, oferta_id, status, nota_virtual, nota_presencial, media, frequencia, confirmada) VALUES ' . implode(',', $inscricaoOfertas);
                    Yii::app()->db->createCommand($sql)->execute();
                }
                // Alunos de especialização
                if ($this->inscricao->ehAlunoDeEspecializacao()) {
                    if (!empty($inscricaoOfertasHabilitacoes)) {
                        $sql = "INSERT INTO habilitacao_inscricao_oferta(habilitacao_id, inscricao_id, oferta_id) VALUES " . implode(',', $inscricaoOfertasHabilitacoes);
                        Yii::app()->db->createCommand($sql)->execute();
                    }
                    if (!empty($inscricaoOfertasHabilitacoesCertificados)) {
                        $sql = "INSERT INTO habilitacao_inscricao_oferta_certificados(habilitacao_id, inscricao_id, oferta_id) VALUES " . implode(',', $inscricaoOfertasHabilitacoesCertificados);
                        Yii::app()->db->createCommand($sql)->execute();
                    }
                }
            } else {
                if (!empty($inscricaoOfertasHabilitacoes)) {
                    $sql = "INSERT INTO habilitacao_inscricao_oferta_certificados(habilitacao_id, inscricao_id, oferta_id) VALUES " . implode(',', $inscricaoOfertasHabilitacoes);
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }

            $sucesso = true;

            $this->gerarLogs($inscricoesHabilitacoesOfertasAntesDaInscricao);
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log("Problema ao salvar as inscrições em ofertas para a inscrição {$this->inscricao->id} {$e->getMessage()} {$e->getTraceAsString()}", 'error', 'system.controllers.AlunoController');
            return false;
        }
        $transaction->commit();

        return $sucesso;
    }

    private function recuperarInformacoesDasInscricoesEmOfertas()
    {
        $sql = "SELECT * FROM inscricao_oferta WHERE inscricao_id = {$this->inscricao->id}";
        $resultados = Yii::app()->db->createCommand($sql)->queryAll();
        $informacoesPorOferta = [];
        foreach ($resultados as $resultado) {
            $ofertaId = $resultado['oferta_id'];
            $informacoesPorOferta[$ofertaId] = $resultado;
        }
        return $informacoesPorOferta;
    }

    private function recuperarInscricoesHabilitacoesEmOfertas()
    {
        if ($this->inscricao->ehAlunoDeEspecializacao()) {
            $sql = "SELECT CONCAT(habilitacao_id, '_', oferta_id) AS habilitacao_oferta FROM habilitacao_inscricao_oferta WHERE inscricao_id = {$this->inscricao->id}";
            $resultados = Yii::app()->db->createCommand($sql)->queryAll();
            return array_map(function($resultado) {
                return $resultado['habilitacao_oferta'];
            }, $resultados);
        } else {
            $sql = "SELECT oferta_id FROM inscricao_oferta WHERE inscricao_id = {$this->inscricao->id}";
            $resultados = Yii::app()->db->createCommand($sql)->queryAll();
            return array_map(function($resultado) {
                return $resultado['oferta_id'];
            }, $resultados);
        }
    }

    private function recuperarInscricoesHabilitacoesEmOfertasParaCertificados()
    {
        if ($this->inscricao->ehAlunoDeEspecializacao()) {
            $sql = "SELECT CONCAT(habilitacao_id, '_', oferta_id) AS habilitacao_oferta FROM habilitacao_inscricao_oferta_certificados WHERE inscricao_id = {$this->inscricao->id}";
            $resultados = Yii::app()->db->createCommand($sql)->queryAll();
            return array_map(function($resultado) {
                return $resultado['habilitacao_oferta'];
            }, $resultados);
        }
        return [];
    }

    private function tratarComoBooleano($string)
    {
        if (is_null($string)) return 'NULL';
        if ($string) return 'TRUE';
        else if (!$string) return 'FALSE';
        return 'NULL';
    }

    private function gerarLogs($inscricoesAntesDeSalvar)
    {
        if ($this->inscricao->ehAlunoDeEspecializacao()) {
            $inscricoesASeremSalvas = [];
            foreach ($this->idsDasOfertasEHabilitacoesASeremInscritas as $ofertaId => $habilitacoes) {
                foreach ($habilitacoes as $habilitacao) {
                    $inscricoesASeremSalvas[] = "{$habilitacao}_{$ofertaId}";
                }
            }

            $inscricoesRemovidas = array_diff($inscricoesAntesDeSalvar, $inscricoesASeremSalvas);
            $inscricoesNovas = array_diff($inscricoesASeremSalvas, $inscricoesAntesDeSalvar);

            foreach ($inscricoesRemovidas as $inscricaoRemovida) {
                [ $habilitacaoId, $ofertaId ] = explode('_', $inscricaoRemovida);
                $mensagem = $this->selecaoDeComponentesParaCertificados
                    ? "Inscrição {$this->inscricao->id} deselecionou oferta {$ofertaId} para aparecer no certificado da {$habilitacaoId}"
                    : "Inscrição {$this->inscricao->id} se desinscreveu da oferta {$ofertaId} pela habilitação {$habilitacaoId}";
                Yii::log($mensagem, 'info', 'system.controllers.AlunoController');
            }
            foreach ($inscricoesNovas as $inscricoesNova) {
                [ $habilitacaoId, $ofertaId ] = explode('_', $inscricoesNova);
                $mensagem = $this->selecaoDeComponentesParaCertificados
                    ? "Inscrição {$this->inscricao->id} selecionou oferta {$ofertaId} para aparecer no certificado da {$habilitacaoId}"
                    : "Inscrição {$this->inscricao->id} se inscreveu na oferta {$ofertaId} pela habilitação {$habilitacaoId}";
                Yii::log($mensagem, 'info', 'system.controllers.AlunoController');
            }
        } else {
            $inscricoesASeremSalvas = array_keys($this->idsDasOfertasEHabilitacoesASeremInscritas);
            $inscricoesRemovidas = array_diff($inscricoesAntesDeSalvar, $inscricoesASeremSalvas);
            $inscricoesNovas = array_diff($inscricoesASeremSalvas, $inscricoesAntesDeSalvar);

            foreach ($inscricoesRemovidas as $inscricaoRemovida) {
                Yii::log("Inscrição {$this->inscricao->id} se desinscreveu da oferta {$inscricaoRemovida}", 'info', 'system.controllers.AlunoController');
            }
            foreach ($inscricoesNovas as $inscricoesNova) {
                Yii::log("Inscrição {$this->inscricao->id} se inscreveu na oferta {$inscricoesNova}", 'info', 'system.controllers.AlunoController');
            }
        }
    }

    public function recuperarMensagensDeErro()
    {
        return $this->mensagensDeErro;
    }

}
