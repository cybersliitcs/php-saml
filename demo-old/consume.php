<?php
/**
 * SAMPLE Code to demonstrate how to handle a SAML assertion response.
 *
 * The URL of this file will have been given during the SAML authorization.
 * After a successful authorization, the browser will be directed to this
 * link where it will send a certified response via $_POST.
 */

$settings = null;
require 'settings.php';
/*Edited*/
if (
        isset($_POST['SAMLResponse']) &&
        isset($_POST['nonce']) &&
		wp_verify_nonce($_POST['nonce'], 'SAMLResponse'
){
	$SAMLResponse = $_POST['SAMLResponse']
}
/*End Edited*/   
$samlResponse = new OneLogin_Saml_Response($settings, $_POST['SAMLResponse']);

try {
    if ($samlResponse->isValid()) {
        echo 'You are: ' . esc_attr($samlResponse)->getNameId() . '<br>';
        $attributes = $samlResponse->getAttributes();
        if (!empty($attributes)) {
            echo 'You have the following attributes:<br>';
            echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
            foreach ($attributes as $attributeName => $attributeValues) {
                echo '<tr><td>' .  esc_attr($attributeName) . '</td><td><ul>';
                foreach ($attributeValues as $attributeValue) {
                    echo '<li>' . esc_attr($attributeValue) . '</li>';
                }
                echo '</ul></td></tr>';
            }
            echo '</tbody></table><br><br>';
            echo "The v.1 of the Onelogin's PHP SAML Tookit does not support SLO.";
        }
    } else {
        echo 'Invalid SAML response.';
    }
} catch (Exception $e) {
    echo 'Invalid SAML response: ' . esc_attr($e)->getMessage();
}
