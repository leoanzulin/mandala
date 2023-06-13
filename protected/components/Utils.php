<?php

/**
 * Classe que contém funções auxiliares
 */
class Utils
{
    public static function tornarColaborador($cpf)
    {
        self::tornarPapel(Constantes::PAPEL_COLABORADOR, $cpf);
    }

    public static function tornarAluno($cpf)
    {
        self::tornarPapel(Constantes::PAPEL_ALUNO, $cpf);
    }

    public static function tornarProfessor($cpf)
    {
        self::tornarPapel(Constantes::PAPEL_PROFESSOR, $cpf);
    }

    public static function tornarTutor($cpf)
    {
        self::tornarPapel(Constantes::PAPEL_TUTOR, $cpf);
    }

    public static function tornarOrientador($cpf)
    {
        self::tornarPapel(Constantes::PAPEL_ORIENTADOR, $cpf);
    }

    private static function tornarPapel($papel, $cpf)
    {
        Yii::app()->authManager->assign($papel, $cpf);
    }
}
