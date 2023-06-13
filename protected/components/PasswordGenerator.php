<?php

/**
 * Responsável por gerar senhas e hashes para o sistema.
 */
class PasswordGenerator
{

        /*
	 * Gera o hash MD5 da senha passada como parâmetro e faz a adequação para o
	 * formato do LDAP.
	 *
	 * @param string $senha Senha a ser hasheada
	 * @return string Hash MD5 da senha no formato adequado para o LDAP
	 */
        public static function sha1($senha)
        {
		return sha1($senha);
	}

}
