<?php

/**
 * WampServerの機能確認サンプル
 *
 * Url:
 * /ratchet/wamp/api
 * 
 * TODO: 同一ブラウザで複数タブを開いた時のコネクション共有は可能か
 * 
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Ratchet_Wamp_Api extends Controller_Base
{

	/**
	 * 前処理
	 */
	public function before()
	{
		parent::before();

		Asset::css('wamp_api.css', array(), 'local');
		Asset::js('autobahn.min.js', array(), 'local');

	}

	/**
	 * コンソール
	 */
	public function action_index()
	{
		$view = View::forge('ratchet/wamp/api/index');
		$view->set_global('title', 'API Console');
		$this->template->content = $view;
	}

}
