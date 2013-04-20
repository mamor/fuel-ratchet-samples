<?php

/**
 * 共通コントーローラ
 *
 * @author    Mamoru Otsuka http://madroom-project.blogspot.jp/
 * @copyright 2013 Mamoru Otsuka
 * @license   MIT License http://www.opensource.org/licenses/mit-license.php
 */
class Controller_Base extends Controller_Template
{

	/**
	 * 前処理
	 * 
	 * @throws Exception
	 */
	public function before()
	{
		if (Input::method() != 'GET')
		{
			if ( ! Security::check_token())
			{
//				throw new Exception('Invalid security token.');
				Response::redirect();
			}
		}

		parent::before();

		Asset::js('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', array(), 'global');

		Asset::add_path('assets/app/css/', 'css');
		Asset::add_path('assets/app/js/', 'js');
		Asset::add_path('assets/app/img/', 'img');

		Asset::css('app.css', array(), 'global');

		Asset::add_path('assets/bootstrap/css/', 'css');
		Asset::add_path('assets/bootstrap/js/', 'js');
		Asset::add_path('assets/bootstrap/img/', 'img');

		Asset::css('bootstrap.min.css', array(), 'global');
		Asset::css('bootstrap-responsive.min.css', array(), 'global');
		Asset::js('bootstrap.min.js', array(), 'global');

		Asset::add_path('assets/Font-Awesome/css/', 'css');
		Asset::css('font-awesome.min.css', array(), 'global');

	}

	/**
	 * 404
	 */
	public function action_404()
	{
		return Response::redirect();
	}

}
