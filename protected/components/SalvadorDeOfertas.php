<?php

/**
 * Componente responsável por fazer o salvamento do gerenciamento de ofertas.
 * Chamado em OfertaController.
 */
class SalvadorDeOfertas
{

    public function salvarOfertas($ofertasJsonEncodadas, $ofertasADeletar)
    {
        DocenteOferta::model()->deleteAll();
        TutorOferta::model()->deleteAll();
        $this->deletarOfertasRemovidas($ofertasADeletar);

        $ofertas = array_map(function($ofertaJsonEncodada) {
            $ofertaJsonString = urldecode($ofertaJsonEncodada);
            $oferta = Oferta::model()->fromJson($ofertaJsonString);
            if (empty($oferta->turma)) $oferta->turma = Constantes::TURMA_ABERTA;
            return $oferta;
        }, $ofertasJsonEncodadas);

        return $this->atualizarOuSalvarNovasOfertas($ofertas);
    }

    private function deletarOfertasRemovidas($ofertasADeletar)
    {
        if (!empty($ofertasADeletar)) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['OfertasADeletar']);
            Oferta::model()->deleteAll($criteria);
        }
    }

    private function atualizarOuSalvarNovasOfertas($ofertas)
    {
        $erros = array();

        foreach ($ofertas as $oferta) {
            if (isset($oferta->id)) {
                $this->atualizarAtributosDaOferta($oferta);
            } else {
                $oferta->save();
            }
            $this->associarDocentes($oferta->docentesArray, $oferta);
            $this->associarTutores($oferta->tutoresArray, $oferta);
        }

        return $erros;
    }

    private function atualizarAtributosDaOferta($ofertaAtualizada)
    {
        $ofertaNoBanco = Oferta::model()->findByPk($ofertaAtualizada->id);
        // https://stackoverflow.com/questions/20995113/duplicate-an-ar-record-re-insert-this-into-the-database
        $ofertaNoBanco->attributes = $ofertaAtualizada->attributes;
        $ofertaNoBanco->update();
    }

    private function associarDocentes($cpfsDocentes, Oferta $oferta)
    {
        foreach ($cpfsDocentes as $cpf) {
            $oferta->associarDocenteCpf($cpf);
        }
    }

    private function associarTutores($cpfsTutores, Oferta $oferta)
    {
        foreach ($cpfsTutores as $cpf) {
            $oferta->associarTutorCpf($cpf);
        }
    }

}
