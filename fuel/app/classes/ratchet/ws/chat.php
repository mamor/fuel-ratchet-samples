<?php

/**
 * WsServerを用いた単一部屋チャットサンプル
 *
 * Run:
 * $ php oil r ratchet:ws Ratchet_Ws_Chat
 * 
 * TODO: 同一ブラウザで複数タブを開いた時のコネクション共有は可能か
 * 
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Ratchet_Ws_Chat extends Ratchet_Ws
{

	/**
	 * メンバー一覧
	 * リソースID => 名前
	 * 
	 * @var type array
	 */
	private static $members = array();

	/**
	 *Validationインスタンス
	 * 
	 * @var type Validation
	 */
	private $validation = null;

	/**
	 * コンストラクタ
	 * 
	 * TODO: \SplObjectStorageの確認
	 */
	public function __construct() {
		//各コネクションが入る
		$this->clients = new \SplObjectStorage;

		//メッセージ受信時に用いる
		$this->validation = Validation::forge('onMessage');
		$this->validation->add('msg')
			->add_rule('trim')
			->add_rule('required')
			->add_rule('max_length', 20);
	}

	/**
	 * 接続
	 * 
	 * @param \Ratchet\ConnectionInterface $conn
	 */
	public function onOpen(\Ratchet\ConnectionInterface $conn) {
		// コネクション毎のセッションを設定
		parent::onOpen($conn);

		// 不正なアクセス
		if ( ! $conn->session instanceof Session_Driver)
		{
			return;
		}

		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('before members : '.print_r(static::$members, true));
		Log::debug('join resourceId : '.$conn->resourceId);
		Log::debug('join name : '.$conn->session->get('ratchet.ws.chat.username'));

		// メンバー一覧に入室者を追加
		static::$members[$conn->resourceId] = $conn->session->get('ratchet.ws.chat.username');

		Log::debug('after members : '.print_r(static::$members, true));
		Log::debug('********** '.__FUNCTION__.' end **********');

		// 入室者にメンバー一覧を送信
		$array['type'] = 'open';
		$array['resource_id'] = $conn->resourceId;
		$array['username'] = $conn->session->get('ratchet.ws.chat.username');

		foreach (static::$members as $resource_id => $username) {
			$array['members'][] = array(
				'resource_id' => $resource_id,
				'username' => $username,
			);
		}

		$conn->send(json_encode(Security::htmlentities($array)));

		// 既存メンバーに入室者を送信
		foreach ($this->clients as $client) {
			$array = array(
				'type' => 'join',
				'resource_id' => $conn->resourceId,
				'username' => $conn->session->get('ratchet.ws.chat.username'),
			);
			$client->send(json_encode(Security::htmlentities($array)));
		}

		$this->clients->attach($conn);
	}

	/**
	 * 切断
	 * 
	 * @param \Ratchet\ConnectionInterface $conn
	 */
	public function onClose(\Ratchet\ConnectionInterface $conn) {
		parent::onClose($conn);

		// 不正なアクセス
		if ( ! $conn->session instanceof Session_Driver)
		{
			return;
		}

		Log::debug('********** '.__FUNCTION__.' begin **********');
		Log::debug('before members : '.print_r(static::$members, true));
		Log::debug('leave resourceId : '.$conn->resourceId);
		Log::debug('leave name : '.$conn->session->get('ratchet.ws.chat.username'));

		// メンバー一覧から退室者を削除
		unset(static::$members[$conn->resourceId]);

		Log::debug('after members : '.print_r(static::$members, true));
		Log::debug('********** '.__FUNCTION__.' end **********');

		$this->clients->detach($conn);

		// 既存メンバーに退室者を送信
		foreach ($this->clients as $client) {
			$array = array(
				'type' => 'leave',
				'resource_id' => $conn->resourceId,
				'username' => $conn->session->get('ratchet.ws.chat.username'),
			);
			$client->send(json_encode(Security::htmlentities($array)));
		}

	}

	/**
	 * メッセージ送受信
	 * 
	 * @param \Ratchet\ConnectionInterface $from
	 * @param type $msg
	 */
	public function onMessage(\Ratchet\ConnectionInterface $from, $msg) {
		parent::onMessage($from, $msg);

		// 不正なアクセス
		if ( ! $from->session instanceof Session_Driver)
		{
			return;
		}

		// バリデーション
		if ( ! $this->validation->run(array('msg' => $msg)))
		{
			$array = array(
				'type' => 'error',
				'errors' => (array) $this->validation->error(),
			);

			$from->send(json_encode(Security::htmlentities($array)));

			return;
		}

		foreach ($this->clients as $client) {
//			if ($from != $client) { // 本人には送信しない
				$array = array(
					'type' => 'msg',
					'resource_id' => $from->resourceId,
					'username' => $from->session->get('ratchet.ws.chat.username'),
					'msg' => Str::sub($msg, 0, 20),
					'posted_at' => time(),
				);
				$client->send(json_encode(Security::htmlentities($array)));
//			}
		}
	}

	/**
	 * TODO: 発生タイミングを確認
	 * 
	 * @param \Ratchet\ConnectionInterface $conn
	 * @param \Exception $e
	 */
	public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
		parent::onError($conn, $e);

		$conn->close();
	}

}

/* end of file chat.php */
