<?php

class GoogleAuthenticatorLoginForm extends MemberLoginForm {

	/**
	 * This field is used in the "You are logged in as %s" message
	 * @var string
	 */
	public $loggedInAsField = 'Email';

	protected $authenticator_class = 'GoogleAuthenticator';

	/**
	 * Taken from MemberLoginForm::__construct with minor changes
	 */
	public function __construct($controller, $name, $fields = null, $actions = null,
								$checkCurrentUser = true) {

		$customCSS = project() . '/css/member_login.css';
		if(Director::fileExists($customCSS)) {
			Requirements::css($customCSS);
		}

		if(isset($_REQUEST['BackURL'])) {
			$backURL = $_REQUEST['BackURL'];
		} else {
			$backURL = Session::get('BackURL');
		}

		if($checkCurrentUser && Member::currentUser() && Member::logged_in_session_exists()) {
			$fields = new FieldList(
				new HiddenField("AuthenticationMethod", null, $this->authenticator_class, $this)
			);
			$actions = new FieldList(
				new FormAction("logout", _t('Member.BUTTONLOGINOTHER', "Log in as someone else"))
			);
		} else {
			if(!$fields) {
				$fields = new FieldList(
					new HiddenField("AuthenticationMethod", null, $this->authenticator_class, $this)
				);
			}
			if(!$actions) {
				$actions = new FieldList(
					// Only "Log in with Google" button
					new FormAction('dologin', _t('GoogleAuthenticator.BUTTONLOGIN', "Log in with Google"))
				);
			}
		}

		if(isset($backURL)) {
			$fields->push(new HiddenField('BackURL', 'BackURL', $backURL));
		}

		// Allow GET method for callback
		$this->setFormMethod('GET', true);

		parent::__construct($controller, $name, $fields, $actions);
	}

	/**
	 * Redirects to the Google Auth URL, the actual authentication is done in GoogleAuthenticatorController::callback() 
	 * after the Google authentication
	 */
	public function dologin($data) {
		$redirectUri = 'http' . (isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS'] ? 's' : '') : '') . '://' . $_SERVER['HTTP_HOST'] . '/GoogleAuthenticatorController/callback';

		$client = new Google_Client();
		$client->setClientId(GOOGLE_AUTHENTICATOR_CLIENT_ID);
		$client->setClientSecret(GOOGLE_AUTHENTICATOR_CLIENT_SECRET);
		$client->setRedirectUri($redirectUri);
		$client->addScope("email");

		$authUrl = $client->createAuthUrl();

		header("Location: $authUrl");
		exit;
	}

}
