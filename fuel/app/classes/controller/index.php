<?php

/**
 * デフォルトコントローラ
 *
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Index extends Controller_Base
{

	/**
	 * HOME画面
	 */
	public function action_index()
	{
		$view = View::forge('index/index');
		$view->set_global('title', 'HOME');
		$this->template->content = $view;
	}

}
