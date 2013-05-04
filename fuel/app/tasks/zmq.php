<?php

namespace Fuel\Tasks;

class Zmq
{
	/**
	 * ヘルプの表示
	 *
	 * Usage (from command line):
	 *
	 * php oil refine zmq
	 */
	public static function run()
	{
		static::help();
	}

	/**
	 * ヘルプの表示
	 *
	 * Usage (from command line):
	 *
	 * php oil refine zmq:help
	 */
	public static function help()
	{
		$output = <<<HELP

Commands:
  php oil refine zmq:push <topic> <msg>
  php oil refine zmq:help

HELP;
		\Cli::write($output);
	}

	/**
	 * ZeroMQを用いてRatchetのWampServerにpushする
	 * 
	 * Usage (from command line):
	 * 
	 * php oil refine zmq:push <topic> <msg>
	 * 
	 * Note:
	 * http://php.zero.mq/
	 * http://socketo.me/docs/push#editblogsubmission
	 */
	public static function push($topic = null, $msg = null, $port = '5555')
	{
		if ($topic === null or $msg === null)
		{
			return;
		}

		$context = new \ZMQContext();
		$socket = $context->getSocket(\ZMQ::SOCKET_PUSH);
		$socket->connect("tcp://localhost:{$port}");

		$socket->send(json_encode(array(
			'topic' => $topic,
			'msg' => $msg,
		)));
	}

}

/* End of file tasks/zmp.php */
