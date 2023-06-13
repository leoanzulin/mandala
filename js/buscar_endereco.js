/**
 * Faz a busca de um endereço a partir do CEP. Utiliza o webservice do
 * site www.republicavirtual.com.br. Tenta buscar o resultado em até 2
 * segundos, se demorar mais que isso interrompe a busca.
 * 
 * Está acoplado às views curso/_formInscricao.php e inscricao/update.
 * TODO: Desacoplar este código.
 */
function desabilitar_endereco(habilitar) {
	$('#Inscricao_endereco').prop('disabled', habilitar);
	$('#Inscricao_complemento').prop('disabled', habilitar);
	$('#Inscricao_cidade').prop('disabled', habilitar);
	$('#Inscricao_estado').prop('disabled', habilitar);
}

$(document).ready(function() {

    $('#Inscricao_cep').blur(function(){

        var cep = $('#Inscricao_cep').val();
        cep = cep.replace(/[^\d]+/g,'');

        if (cep == '' || cep.length != 8) {
			desabilitar_endereco(true);
            return false;
        }

        $('#loading-cep').css('display', 'block');
        
        /*
            Para conectar no servico e executar o json, precisamos usar a funcao
            getScript do jQuery, o getScript e o dataType:'jsonp' conseguem fazer o cross-domain, os outros
            dataTypes nao possibilitam esta interacao entre dominios diferentes
            Estou chamando a url do servico passando o parametro 'formato=javascript' e o CEP digitado no formulario
            http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep='$('#cep').val()
        */

        $.ajax({
            url: 'http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep=' + cep,
            dataType: 'script',
            timeout: 2000,
            success: function() {
				desabilitar_endereco(false);
				$('#Inscricao_endereco').val(unescape(resultadoCEP['tipo_logradouro']) + ' ' + unescape(resultadoCEP['logradouro']));
                if ($('#Inscricao_endereco').val() == ' ')
					$('#Inscricao_endereco').val('');
                $('#Inscricao_cidade').val(unescape(resultadoCEP['cidade']));
                $('#Inscricao_estado').val(unescape(resultadoCEP['uf']));
                
                $('#loading-cep').css('display', 'none');
            },
            error: function() {
				desabilitar_endereco(false);
                $('#Inscricao_endereco').val('');
                $('#Inscricao_complemento').val('');
                $('#Inscricao_cidade').val('');
                $('#Inscricao_estado').val('Escolha o estado');

                $('#loading-cep').css('display', 'none');
            }
        });

    });
    
});
