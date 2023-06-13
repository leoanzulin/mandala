<?php

/**
 * This is the model class for table "configuracao".
 *
 * The followings are the available columns in table 'componente_curricular':
 * @property string $atributo
 * @property string $valor
 */
class Configuracao extends CActiveRecord
{
    // O período é uma string contendo números separados por vírgula ("15,45") indicando quantos dias
    // antes do início da oferta este lembrete deverá ser enviado. "15,45" significa que o lembrete
    // será enviado 45 dias e 15 dias antes do início da oferta.
    const MENSAGEM_LEMBRETE_DOCENTE_PERIODOS = 'mensagem.lembrete_docente_periodos';
    const MENSAGEM_LEMBRETE_DOCENTE_ASSUNTO = 'mensagem.lembrete_docente_assunto';
    const MENSAGEM_LEMBRETE_DOCENTE_CORPO = 'mensagem.lembrete_docente_corpo';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_PERIODOS = 'mensagem.lembrete_aluno_proximo_mes_periodos';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_ASSUNTO = 'mensagem.lembrete_aluno_proximo_mes_assunto';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_CORPO = 'mensagem.lembrete_aluno_proximo_mes_corpo';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_PERIODOS = 'mensagem.lembrete_aluno_proximo_semestre_periodos';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_ASSUNTO = 'mensagem.lembrete_aluno_proximo_semestre_assunto';
    const MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_CORPO = 'mensagem.lembrete_aluno_proximo_semestre_corpo';

    public function tableName()
    {
        return 'configuracao';
    }

    public function rules()
    {
        return array(
            array('atributo, valor', 'required'),
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'atributo' => $this->atributo,
            'valor' => $this->valor,
        );
    }

    public function toJSON()
    {
        return json_encode($this->asArray());
    }

    public static function propriedade($propriedade)
    {
        $configuracao = self::model()->findByPk($propriedade);
        return !empty($configuracao) ? $configuracao->valor : null;
    }

    public static function salvarPropriedade($propriedade, $valor)
    {
        $configuracao = self::model()->findByPk($propriedade);
        if (empty($configuracao)) {
            $configuracao = new Configuracao();
            $configuracao->atributo = $propriedade;
        }
        $configuracao->valor = $valor;
        $configuracao->save();
    }

    /**
     * Retorna um array de quatro posições com o período em que as ofertas estão
     * abertas no sistema, no formato (deMes, deAno, ateMes, ateAno).
     */
    public static function periodoAberto()
    {
        $deMes = self::propriedade('inscricoes.inicio_periodo_aberto.mes');
        $deAno = self::propriedade('inscricoes.inicio_periodo_aberto.ano');
        $ateMes = self::propriedade('inscricoes.fim_periodo_aberto.mes');
        $ateAno = self::propriedade('inscricoes.fim_periodo_aberto.ano');
        return array($deMes, $deAno, $ateMes, $ateAno);
    }
    
}
