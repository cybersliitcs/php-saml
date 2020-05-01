<?php

/**
 *  SP Assertion Consumer Service Endpoint
 */

session_start();

require_once dirname(__DIR__).'/_toolkit_loader.php';

$auth = new OneLogin_Saml2_Auth();

$auth->processResponse();

$errors = $auth->getErrors();

if (!empty($errors)) {
    echo '<p>', esc_attr(', ', $errors), '</p>';
    exit();
}

if (!$auth->isAuthenticated()) {
    echo "<p>Not authenticated</p>";
    exit();
}

$_SESSION['samlUserdata'] = $auth->getAttributes();
$_SESSION['IdPSessionIndex'] = $auth->getSessionIndex();
/*edited*/
if(
	isset( $_POST['RelayState']) && isset( $_POST['nonce'])
	&& wp_verify_nonce($_POST['nonce'], 'RelayState_action')
  ){
  		$RelayState = $_POST['RelayState'];
  }

if ($RelayState) && OneLogin_Saml2_Utils::getSelfURL() != $RelayState) {
    $auth->redirectTo($RelayState);
}
/*end edit*/
$attributes = $_SESSION['samlUserdata'];

if (!empty($attributes)) {
    echo '<h1>'.esc_html('User attributes:').'</h1>';
    echo '<table><thead><th>'.esc_html('Name').'</th><th>'.esc_html('Values').'</th></thead><tbody>';
    foreach ($attributes as $attributeName => $attributeValues) {
        echo '<tr><td>'.esc_attr($attributeName).'</td><td><ul>';
        foreach ($attributeValues as $attributeValue) {
            echo '<li>'.esc_attr($attributeValue).'</li>';
        }
        echo '</ul></td></tr>';
    }
    echo '</tbody></table>';
    if (!empty($_SESSION['IdPSessionIndex'])) {
        echo '<p>The SessionIndex of the IdP is: '.esc_attr($_SESSION['IdPSessionIndex']).'</p>';
    }
} else {
    echo esc_html('Attributes not found');
}
