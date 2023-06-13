<?php

/**
 * This is the model class for table "proposta_pedagogica".
 *
 * The followings are the available columns in table 'proposta_pedagogica':
 * @property int $id
 * @property int $tcc_id
 * @property int $ordem
 * @property string $titulo
 * @property string $nivel_formacao
 * @property string $area_conhecimento
 * @property string $modalidade
 * @property string $nome_ferramenta
 * @property string $descricao_dinamica
 * @property string $descricao_diferenciais
 * @property string $descricao_procedimentos
 * @property string $descricao_reflexao
 * @property string $descricao_abordagem
 * @property string $descricao_referencias
 * @property string $tipo_proposta
 *
 * The followings are the available model relations:
 * @property Tcc $tcc
 */
class PropostaPedagogica extends CActiveRecord
{
    public function tableName()
    {
        return 'proposta_pedagogica';
    }

    public function rules()
    {
        return [
            ['tcc_id, ordem, titulo, nivel_formacao, area_conhecimento, modalidade, nome_ferramenta, descricao_dinamica, descricao_diferenciais, descricao_procedimentos, descricao_reflexao, tipo_proposta', 'required'],
            ['id', 'required', 'on' => 'update'],
            ['ordem', 'numerical', 'integerOnly' => true],
            ['titulo, nivel_formacao, area_conhecimento, modalidade, nome_ferramenta, tipo_proposta', 'length', 'max' => 256],
            ['id, tcc_id, ordem, titulo, nivel_formacao, area_conhecimento, modalidade, nome_ferramenta, descricao_dinamica, descricao_diferenciais, descricao_procedimentos, descricao_reflexao, descricao_abordagem, descricao_referencias, tipo_proposta', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'tcc' => [self::BELONGS_TO, 'Tcc', 'tcc_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Título ou tema da proposta',
            'nivel_formacao' => 'Nível de formação sugerido para a proposta',
            'area_conhecimento' => 'Disciplina ou área do conhecimento indicado',
            'modalidade' => 'Modalidade em que será implementada a proposta',
            'nome_ferramenta' => 'Nome da ferramenta de mediação da proposta escolhida',
            'descricao_dinamica' => 'Descrição da dinâmica de aplicação',
            'descricao_diferenciais' => 'Diferenciais da proposta (vantagens e benefícios)',
            'descricao_procedimentos' => 'Procedimentos de aplicação (passo a passo detalhado de como aplicar)',
            'descricao_reflexao' => 'Reflexão pessoal e comentários sobre a proposta',
            'descricao_abordagem' => 'Abordagem pedagógica da proposta (opcional)',
            'descricao_referencias' => 'Autores, teorias e textos sobre o assunto (opcional)',
            'tipo_proposta' => 'Tipo de proposta ou estratégia',
        ];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function NIVEL_FORMACAO($indice)
    {
        $niveis = [
            'infantil_1' => 'Educação infantil 1 (creche – 0 a 3 anos)',
            'infantil_2' => 'Educação infantil 2 (pré-escolar – 3 a 6 anos)',
            'fundamental_1' => 'Ensino fundamental 1 (1º ao 5º ano)',
            'fundamental_2' => 'Ensino fundamental 2 (6º ao 9º ano)',
            'medio' => 'Ensino médio',
            'superior' => 'Ensino superior',
            'aberta' => 'Educação aberta (informal, não-formal ou livre)',
            'outro' => 'Outro',
        ];
        return $niveis[$indice];
    }

    public static function AREA_CONHECIMENTO($indice)
    {
        $areas = [
            'ciencias' => 'Ciências em geral',
            'artes' => 'Artes',
            'biologia' => 'Biologia',
            'fisica' => 'Física',
            'geografia' => 'Geografia',
            'historia' => 'História',
            'lingua_portuguesa' => 'Língua portuguesa',
            'matematica' => 'Matemática',
            'quimica' => 'Química',
            'tecnologicas' => 'Tecnologias',
            'outra' => 'Outra',
        ];
        return $areas[$indice];
    }

    public static function TIPO_PROPOSTA($indice)
    {
        $codigos = [
            1 => 'Aplicação de atividade pedagógica (em sala de aula ou AVA)',
            2 => 'Manejo de turma na oferta de disciplina em EaD',
            3 => 'Gerenciamento da aprendizagem (manejo de turma/estudantes)',
            4 => 'Coordenação de atividades pedagógicas',
            5 => '-----',
            6 => 'Elaboração de atividades pedagógicas',
            7 => 'Produção de materiais didáticos',
            8 => 'Organização de conteúdos didáticos',
            9 => 'Planejamento de atividades e materiais didáticos',
            10 => 'Preparação e organização de disciplinas em EaD',
            11 => '-----',
            12 => 'Gerenciamento das atividades de professores',
            13 => 'Gerenciamento das atividades de tutores',
            14 => 'Gerenciamento de equipes e recursos humanos',
            15 => 'Gerenciamento de trabalho colaborativo (em grupo)',
            16 => '-----',
            17 => 'Coordenação pedagógica de cursos EaD',
            18 => 'Gerenciamento da produção de materiais didáticos',
            19 => 'Gerenciamento de comunicação e interações',
            20 => 'Gerenciamento de infraestrutura e tecnologias',
            21 => 'Gerenciamento de processos administrativos ou técnicos',
            22 => 'Gerenciamento financeiro',
            23 => 'Gerenciamento logístico (fluxos e processos)',
            24 => '-----',
            25 => 'Outra',
        ];
        return $codigos[$indice];
    }
}
