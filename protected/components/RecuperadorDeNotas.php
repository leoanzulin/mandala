<?php

/**
 * A view notas_do_moodle originalmente buscava notas pelo CPF dos alunos.
 * Hoje, contudo, a busca é feita pelo número UFSCar. Para não ter que
 * redefinir a view no banco de dados do Moodle, o nome do campo 'cpf' se
 * manteve, mas hoje ele representa o número UFSCar.
 */
class RecuperadorDeNotas
{

    public static function recuperarTodasAsNotas()
    {
        $sql = 'SELECT DISTINCT * FROM notas_do_moodle';
        $notas = Yii::app()->dbAva->createCommand($sql)->queryAll();
        self::processar($notas);
    }

    public static function recuperarNotasDoAlunoDeCpf($cpf)
    {
        $inscricao = Inscricao::model()->findByCpf($cpf);
        $sql = "SELECT DISTINCT * FROM notas_do_moodle WHERE cpf = '{$inscricao->numero_ufscar}'";
        $notas = Yii::app()->dbAva->createCommand($sql)->queryAll();
        self::processar($notas);
    }

    public static function recuperarNotasDaOfertaCujoCodigoNoMoodleEh($codigoMoodle)
    {
        $sql = "SELECT DISTINCT * FROM notas_do_moodle WHERE codigo_componente = '{$codigoMoodle}'";
        $notas = Yii::app()->dbAva->createCommand($sql)->queryAll();
        self::processar($notas);
    }

    private static function processar($notas)
    {
        foreach ($notas as $nota) {
            $inscricaoOfertas = InscricaoOferta::encontrarInscricaoOfertas($nota['cpf'], $nota['codigo_componente']);
            if (empty($inscricaoOfertas)) {
                Yii::log("Inscrição do aluno de número UFSCar {$nota['cpf']} na oferta de código {$nota['codigo_componente']} não foi encontrada.", 'error', 'system.components.RecuperadorDeNotas');
                continue;
            }

            foreach ($inscricaoOfertas as $inscricaoOferta) {

                $inscricaoOferta->nota_virtual = $nota['nota_virtual'];
                $inscricaoOferta->nota_presencial = $nota['nota_presencial'];
                $inscricaoOferta->media = $nota['media'];
                $inscricaoOferta->frequencia = $nota['frequencia'];
                // Só atribui o status se nenhum status já tiver sido atribuído.
                // Dessa forma, o status do tipo "Trancado" não é apagado
                if (empty($inscricaoOferta->status)) {
                    $inscricaoOferta->status = $inscricaoOferta->ehAprovada() ? 'Aprovado' : 'Reprovado';
                }

                if (!$inscricaoOferta->save()) {
                    die(var_dump($inscricaoOferta->errors));
                }

            }
        }
    }

}
