<?php

class LogDb extends CDbLogRoute
{

    protected function createLogTable($db, $tableName)
    {
        $db->createCommand()->createTable($tableName, array(
            'id' => 'pk',
            'level' => 'varchar(256)',
            'category' => 'varchar(256)',
            'logtime' => 'timestamp with time zone', 
            'ip' => 'varchar(50)',
            'user_id' => 'varchar(11)',
            'request_url' => 'text',
            'message' => 'text',
            'stacktrace' => 'text',
        ));
    }

    protected function processLogs($logs)
    {
        $command = $this->getDbConnection()->createCommand();
        $logTime = date('Y-m-d H:i:s');
 
        foreach ($logs as $log) {
			$mensagem = $this->separarMensagemDoStacktrace($log[0]);
			
            $command->insert($this->logTableName, array(
                'level' => $log[1],
                'category' => $log[2],
                'logtime' => $logTime,
                'ip' => Yii::app()->request->userHostAddress,
                'user_id' => Yii::app()->user->id,
                'request_url' => Yii::app()->request->url,
                //~ 'message' => $log[0],
                'message' => $mensagem[0],
                //~ 'stacktrace' => $log[0],
                'stacktrace' => $mensagem[1],
            ));
        }
    }

	/**
	 * As mensagens de log do Yii vêm no formato "<mensagem> <stacktrace>". Este
	 * método faz a separação dessas duas partes.
	 * 
	 * @param $mensagem Mensagem de log do Yii
	 * @returns array Array cuja primeira posição é a mensagem, e a segunda é
	 *                o stacktrace.
	 */
	private function separarMensagemDoStacktrace($mensagem)
	{
		$posicaoStacktrace = strpos($mensagem, 'Stack trace:');
		if ($posicaoStacktrace !== false) {
			$stacktrace = substr($mensagem, $posicaoStacktrace);
			$mensagem = substr($mensagem, 0, $posicaoStacktrace - 1);
			return array($mensagem, $stacktrace);
		}

		$posicaoStacktrace = strpos($mensagem, 'in /');
		$stacktrace = substr($mensagem, $posicaoStacktrace);
		$mensagem = substr($mensagem, 0, $posicaoStacktrace - 1);

		return array($mensagem, $stacktrace);
	}

}
