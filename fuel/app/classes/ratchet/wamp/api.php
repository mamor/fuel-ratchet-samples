<?php

/**
 * WampServerの機能確認サンプル
 *
 * Run:
 * $ php oil r ratchet:wamp Ratchet_Wamp_Api
 * 
 * TODO: 同一ブラウザで複数タブを開いた時のコネクション共有は可能か
 * 
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Ratchet_Wamp_Api extends Ratchet_Wamp
{
	/**
	 * トピック一覧
	 * トピックID => トピック
	 * 
	 * @var type array
	 */
	private $topics = array();

	/**
	 * Validationインスタンス群
	 * 
	 * @var type Validation
	 */
	private $validations = null;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		$this->validations['msg'] = Validation::forge('msg');
		$this->validations['msg']->add('msg')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length', 20);

		$this->validations['topic'] = Validation::forge('topic');
		$this->validations['topic']->add('topic')
			->add_rule('match_value', array('topic_1', 'topic_2', 'topic_3'));
	}

	/**
	 * 切断
	 * 
	 * @param  \Ratchet\ConnectionInterface $conn
	 */
	public function onClose(\Ratchet\ConnectionInterface $conn) {
		// 全てのトピックを購読解除
		foreach ($this->topics as $topic)
		{
			$this->onUnSubscribe($conn, $topic);
		}
	}

	/**
	 * 配信
	 * 
	 * @param  \Ratchet\ConnectionInterface $conn
	 * @param  string|\Ratchet\Wamp\Topic $topic
	 * @param  string $event
	 * @param  array $exclude
	 * @param  array $eligible
	 */
	public function onPublish(\Ratchet\ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('$topic : '.$topic);
		Log::debug('$event : '.$event);
		Log::debug('$exclude : '.print_r($exclude, true));
		Log::debug('$eligible : '.print_r($eligible, true));
		Log::debug('********** '.__FUNCTION__.' end **********');

		// 不正なトピック
		if ( ! $this->validations['topic']->run(array('topic' => $topic)))
		{
			Log::error('Invalid topic: '.$topic);
			return;
		}

		$json = json_decode($event);

		// 不正なメッセージ
		if ( ! $this->validations['msg']->run(array('msg' => $json->msg)))
		{
			$errmsg = 'Invalid msg: '.$json->msg;

			Log::error($errmsg);

			$conn->event('error', Security::htmlentities(array(
				'msg' => $errmsg,
			)));

			return;
		}

		// トピックに対する購読者が存在する場合、配信
		if (array_key_exists($topic->getId(), $this->topics))
		{
			$topic->broadcast(Security::htmlentities(
				$this->validations['msg']->validated('msg')));
		}
	}

	/**
	 * 購読
	 * 
	 * @param  \Ratchet\ConnectionInterface $conn
	 * @param  string|\Ratchet\Wamp\Topic $topic
	 */
	public function onSubscribe(\Ratchet\ConnectionInterface $conn, $topic) {
		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('$topic : '.$topic);
		Log::debug('********** '.__FUNCTION__.' end **********');

		// 不正なトピック
		if ( ! $this->validations['topic']->run(array('topic' => $topic)))
		{
			Log::error('Invalid topic: '.$topic);
			return;
		}

		// トピック一覧にトピックを追加
		if (!array_key_exists($topic->getId(), $this->topics))
		{
			$this->topics[$topic->getId()] = $topic;
		}
	}
 
	/**
	 * 購読解除
	 * 
	 * @param  \Ratchet\ConnectionInterface $conn
	 * @param  string|\Ratchet\Wamp\Topic $topic
	 */
	public function onUnSubscribe(\Ratchet\ConnectionInterface $conn, $topic) {
		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('$topic : '.$topic);
		Log::debug('********** '.__FUNCTION__.' end **********');
 
		// 不正なトピック
		if ( ! $this->validations['topic']->run(array('topic' => $topic)))
		{
			Log::error('Invalid topic: '.$topic);
			return;
		}
 
		// トピックからコネクションを削除
		$topic->remove($conn);
 
		// トピックの購読者が存在しない場合、トピック一覧からトピックを削除
		if ($topic->count() == 0)
		{
			unset($this->topics[$topic->getId()]);
		}
 
	}
 
	/**
	 * RPC
	 * 
	 * @param  \Ratchet\ConnectionInterface $conn
	 * @param  string $id
	 * @param  string|\Ratchet\Wamp\Topic $fn
	 * @param  array $params
	 * @return \Ratchet\Wamp\WampConnection
	 */
	public function onCall(\Ratchet\ConnectionInterface $conn, $id, $fn, array $params) {
		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('$id : '.$id);
		Log::debug('$fn : '.$fn);
		Log::debug('$params : '.print_r($params, true));
		Log::debug('********** '.__FUNCTION__.' end **********');

		switch ($fn) {
			case 'ping':
				// TODO: 放置するとコネクションが切れるので、暫定的な対策
			break;

			// 購読しているトピック一覧を取得
			case 'get_subscribing_topics':
				$subscribing_topics = array();

				Log::debug('********** Topics begin **********');

				foreach ($this->topics as $topic)
				{
					Log::debug('$topic : '.$topic);
					Log::debug('$topic->count() : '.$topic->count());

					$topic->has($conn) and $subscribing_topics[] = $topic;
				}

				Log::debug('********** Topics end **********');

				return $conn->callResult($id, Security::htmlentities($subscribing_topics));
			break;

			// エラー処理
			default:
				$errorUri = 'errorUri';
				$desc = 'desc';
				$details = 'details';

				/**
				 * \Ratchet\Wamp\WampConnection
				 * 
				 * callError($id, $errorUri, $desc = '', $details = null)
				 */
				return $conn->callError($id, $errorUri, $desc, $details);
			break;
		}
	}

	/**
	 * ZeroMQ経由でコールされる
	 * 
	 * $ php oil r zmq:push <topic> <msg> で確認可能
	 * 
	 * @param  string $msg
	 */
	public function zmqCallback($msg) {
		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('$json_string : '.$msg);
		Log::debug('********** '.__FUNCTION__.' end **********');

		$json = json_decode($msg);

		// 不正な呼出
		if( ! isset($json->topic) || ! isset($json->msg))
		{
			Log::error('Invalid call.');
			return;
		}

		// 不正なトピック
		if ( ! $this->validations['topic']->run(array('topic' => $json->topic)))
		{
			Log::error('Invalid topic: '.$topic);
			return;
		}

		// 不正なメッセージ
		if ( ! $this->validations['msg']->run(array('msg' => $json->msg)))
		{
			Log::error('Invalid msg: '.$json->msg);
			return;
		}

		foreach ($this->topics as $topic)
		{
			if ($json->topic == $topic)
			{
				// 配信
				$topic->broadcast(Security::htmlentities(
					$this->validations['msg']->validated('msg')));
				break;
			}
		}
	}

}

/* end of file api.php */
