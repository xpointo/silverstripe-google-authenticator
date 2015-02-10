<?php

class GoogleAuthenticator extends MemberAuthenticator {

	public static function get_login_form(Controller $controller) {
		return GoogleAuthenticatorLoginForm::create($controller, "LoginForm");
	}

	public static function get_cms_login_form(\Controller $controller) {
		return GoogleAuthenticatorLoginForm::create($controller, "LoginForm");
	}

	public static function get_name() {
		return _t('GoogleAuthenticator.TITLE', "Google");
	}

}
