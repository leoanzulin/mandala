<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'proposta-pedagogica-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <?php echo $form->errorSummary($propostaPedagogica); ?>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'titulo'); ?>
        <?php echo $form->textField($propostaPedagogica, 'titulo'); ?>
        <?php echo $form->error($propostaPedagogica, 'titulo'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'nivel_formacao'); ?>
        <?php
        echo $form->dropDownList(
            $propostaPedagogica,
            'nivel_formacao',
            [
                'infantil_1' => 'Educação infantil 1 (creche – 0 a 3 anos)',
                'infantil_2' => 'Educação infantil 2 (pré-escolar – 3 a 6 anos)',
                'fundamental_1' => 'Ensino fundamental 1 (1º ao 5º ano)',
                'fundamental_2' => 'Ensino fundamental 2 (6º ao 9º ano)',
                'medio' => 'Ensino médio',
                'superior' => 'Ensino superior',
                'aberta' => 'Educação aberta (informal, não-formal ou livre)',
                'outro' => 'Outro',
            ],
            ['empty' => 'Nível de formação']
        );
        ?>
        <?php echo $form->error($propostaPedagogica, 'nivel_formacao'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'area_conhecimento'); ?>
        <?php
        echo $form->dropDownList(
            $propostaPedagogica,
            'area_conhecimento',
            [
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
            ],
            ['empty' => 'Disciplina ou área de conhecimento']
        );
        ?>
        <?php echo $form->error($propostaPedagogica, 'area_conhecimento'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'modalidade'); ?>
        <?php
        echo $form->dropDownList(
            $propostaPedagogica,
            'modalidade',
            [
                'presencial' => 'Educação presencial',
                'distância' => 'Educação a Distância',
                'outra' => 'Outra',
            ],
            ['empty' => 'Modalidade']
        );
        ?>
        <?php echo $form->error($propostaPedagogica, 'modalidade'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'nome_ferramenta'); ?>
        <?php echo $form->textField($propostaPedagogica, 'nome_ferramenta'); ?>
        <?php echo $form->error($propostaPedagogica, 'nome_ferramenta'); ?>
    </div>

    <fieldset>
        <legend>Descrição da proposta de aplicação</legend>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_dinamica'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_dinamica', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_dinamica'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_diferenciais'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_diferenciais', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_diferenciais'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_procedimentos'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_procedimentos', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_procedimentos'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_reflexao'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_reflexao', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_reflexao'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_abordagem'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_abordagem', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_abordagem'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($propostaPedagogica, 'descricao_referencias'); ?>
            <?php echo $form->textArea($propostaPedagogica, 'descricao_referencias', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
            <?php echo $form->error($propostaPedagogica, 'descricao_referencias'); ?>
        </div>

    </fieldset>

    <hr>

    <div class="row">
        <?php echo $form->labelEx($propostaPedagogica, 'tipo_proposta'); ?>
        <?php
        echo $form->dropDownList(
            $propostaPedagogica,
            'tipo_proposta',
            [
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
            ],
            ['empty' => 'Selecionar tipo de proposta ou estratégia']
        );
        ?>
        <?php echo $form->error($propostaPedagogica, 'tipo_proposta'); ?>
    </div>

    <div class="row buttons" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>
    <?php if (!$propostaPedagogica->isNewRecord) { ?>
        <div class="row buttons" style="margin-top: 10px">
            <label></label>
            <?php echo CHtml::submitButton('Excluir proposta', [
                'class' => 'btn btn-danger btn-lg',
                'name' => 'excluir',
                'onClick' => 'js:return confirm("Tem certeza que deseja excluir esta proposta?");',
            ]); ?>
        </div>
    <?php } ?>

    <?php $this->endWidget(); ?>

</div>