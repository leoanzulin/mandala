<?php

/**
 * Controlador que implementa uma interface REST para o modelo Oferta.
 * 
 * Métodos:
 * 
 */
class OfertaController extends Controller
{

    /**
     * API REST
     * 
     * Retorna todas as ofertas do sistema organizadas por períodos. As ofertas
     * em que o usuário logado no momento está inscrito são marcadas. Utilizado
     * nas views aluno/inscricao.
     */
    public function actionPorPeriodo()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeOfertas
                ->daInscricao($inscricao->id)
                ->semCamposRelacionados()
                ->recuperar();
        $this->respostaJSON(json_encode($ofertasPorPeriodos));
    }

    /**
     * API REST
     *
     * Retorna as ofertas na qual o usuário logado está matriculado e aprovado para que
     * seja feita a seleção de componentes de cada certificado de conclusão de curso.
     */
    public function actionSelecaoParaCertificados()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $recuperadorDeofertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeofertas
                ->daInscricao($inscricao->id)
                ->semCamposRelacionados()
                ->manterApenasInscritas()
                ->manterApenasOfertasAprovadas()
                ->escolherComponentesParaCertificados()
                ->recuperar();
        $this->respostaJSON(json_encode($ofertasPorPeriodos));
    }

    /**
     * API REST
     *
     * Retorna as ofertas na qual uma determinada inscrição está matriculada e aprovada para que
     * seja feita a seleção de componentes de cada certificado de conclusão de curso.
     *
     * @param int $inscricaoId
     */
    public function actionSelecaoParaCertificadosDaInscricao($inscricaoId)
    {
        $recuperadorDeofertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeofertas
                ->daInscricao($inscricaoId)
                ->semCamposRelacionados()
                ->manterApenasInscritas()
                ->manterApenasOfertasAprovadas()
                ->escolherComponentesParaCertificados()
                ->recuperar();
        $this->respostaJSON(json_encode($ofertasPorPeriodos));
    }

    /**
     * API REST
     * 
     * Retorna todas as ofertas do sistema organizadas por períodos. As ofertas
     * em que a inscrição cujo ID é passado como parâmetro está inscrita são
     * marcadas. Utilizado nas views do admin.
     * 
     * @param int $inscricaoId
     */
    public function actionPorPeriodoDaInscricao($inscricaoId)
    {
        $recuperadorDeofertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeofertas
                ->daInscricao($inscricaoId)
                ->recuperar();
        $this->respostaJSON(json_encode($ofertasPorPeriodos));
    }

    /**
     * API REST
     * 
     * Retorna todas as ofertas do sistema, organizadas por períodos.
     * 
     * Action chamada pelo administrador.
     */
    public function actionTodasPorPeriodo()
    {
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeOfertas->recuperar();
        $this->respostaJSON(json_encode($ofertasPorPeriodos));
    }

    /**
     * API REST
     * 
     * Altera o mês e ano de uma determinada oferta.
     */
    public function actionAlterarPeriodo($id, $ano, $mes)
    {
        $oferta = Oferta::model()->findByPk($id);
        $oferta->ano = $ano;
        $oferta->mes = $mes;
        $oferta->saveAttributes(array('ano', 'mes'));
        $this->respostaJSON(json_encode('OK'));
    }

    public function actionGerenciar()
    {
        if (isset($_POST['Ofertas'])) {

            $salvadorDeOfertas = new SalvadorDeOfertas();
            $erros = $salvadorDeOfertas->salvarOfertas($_POST['Ofertas'], isset($_POST['OfertasADeletar']) ? $_POST['OfertasADeletar'] : null);

            if (empty($erros)) {
                Yii::app()->user->setFlash('notificacao', 'Ofertas de componentes salvas com sucesso!');
                Yii::log("Administrador salvou ofertas de componentes.", 'info', 'system.controllers.OfertaController');
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao salvar as ofertas de componentes');
                Yii::log("Ocorreram problemas ao salvar as ofertas de componentes. Erros: " . print_r($erros, true), 'error', 'system.controllers.OfertaController');
            }
            $this->redirect(array('gerenciar'));
        }

        $this->adicionarArquivosJavascript('/js/modulos/gerenciarOfertasApp');

        $this->render('gerenciar', array(
            'habilitacoes' => Habilitacao::findAllValid(),
        ));
    }

}
