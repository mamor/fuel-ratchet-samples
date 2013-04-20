<?php

/**
 * WsServerを用いた単一部屋チャットサンプル
 *
 * Url:
 * /ratchet/ws/chat/{action}
 * 
 * TODO: 同一ブラウザで複数タブを開いた時のコネクション共有は可能か
 * 
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Ratchet_Ws_Chat extends Controller_Base
{

	/**
	 * 前処理
	 */
	public function before()
	{
		parent::before();

		Asset::css('ws_chat.css', array(), 'local');
	}

	/**
	 * チャットルーム
	 */
	public function action_room()
	{
		$username = Session::get('ratchet.ws.chat.username', false);
		! $username and Response::redirect('ratchet/ws/chat/login');

		$view = View::forge('ratchet/ws/chat/room');
		$view->set_global('title', 'Single ChatRoom');
		$this->template->content = $view;
	}

	/**
	 * ログイン
	 */
	public function action_login()
	{
		if (Input::method() == 'POST')
		{
			$v = Validation::forge();

			$v->add('username')
				->add_rule('trim')
				->add_rule('required')
				->add_rule('valid_string', array('alpha', 'numeric'))
				->add_rule('max_length', 10);

			if ($v->run())
			{
				Session::set('ratchet.ws.chat.username', $v->validated('username'));
				Response::redirect('ratchet/ws/chat/room');
			}

			Session::set_flash('errors', $v->error());
		}

		$view = View::forge('ratchet/ws/chat/login');
		$view->set_global('title', 'Single ChatRoom Login');
		$this->template->content = $view;
	}

	/**
	 * ログアウト
	 */
	public function action_logout()
	{
		Session::destroy();
		Response::redirect('ratchet/ws/chat/login');
	}

}
