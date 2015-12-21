<?php

class GoogleAuthenticatorController extends Controller
{

    private static $allowed_actions = array(
        'callback',
    );

    public function callback()
    {
        $redirectUri = 'http' . (isset($_SERVER['HTTPS']) ? ($_SERVER['HTTPS'] ? 's' : '') : '') . '://' . $_SERVER['HTTP_HOST'] . '/GoogleAuthenticatorController/callback';

        $client = new Google_Client();
        $client->setClientId(GOOGLE_AUTHENTICATOR_CLIENT_ID);
        $client->setClientSecret(GOOGLE_AUTHENTICATOR_CLIENT_SECRET);
        $client->setRedirectUri($redirectUri);
        $client->addScope("email");

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['google_accesstoken'] = $client->getAccessToken();
            header('Location: ' . filter_var($redirectUri, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['google_accesstoken']) && $_SESSION['google_accesstoken']) {
            $client->setAccessToken($_SESSION['google_accesstoken']);
        }

        $form = new GoogleAuthenticatorLoginForm($this, 'LoginForm');

        if ($client->getAccessToken() && !$client->isAccessTokenExpired()) {
            $_SESSION['google_accesstoken'] = $client->getAccessToken();
            $token_data = $client->verifyIdToken()->getAttributes();
            $email = $token_data['payload']['email'];

            $member = Member::get()->filter(array('Email' => $email))->first();

            if (isset($_SESSION['BackURL']) && $_SESSION['BackURL'] && Director::is_site_url($_SESSION['BackURL'])) {
                $backURL = $_SESSION['BackURL'];
            }

            if ($member) {
                $member->logIn();

                if ($backURL) {
                    return $this->redirect($backURL);
                }

                if (Security::config()->default_login_dest) {
                    return $this->redirect(Director::absoluteBaseURL() . Security::config()->default_login_dest);
                }

                return Controller::curr()->redirectBack();
            } else {
                $form->sessionMessage("The Google account $email is not authorised to access the system.", 'bad');
            }
        } else {
            $form->sessionMessage("There is an error authenticating with Google. Please try again.", 'bad');
        }

        $loginLink = Director::absoluteURL('/Security/login');
        if ($backURL) {
            $loginLink .= '?BackURL=' . urlencode($backURL);
        }
        $loginLink .= '#GoogleAuthenticatorLoginForm_LoginForm_tab';
        return $this->redirect($loginLink);
    }
}
