<?php

$config = array(
  // This is a authentication source which handles admin authentication.
  'admin' => array(
    // The default is to use core:AdminPassword, but it can be replaced with any authentication source.
    'core:AdminPassword',
  ),

  // An authentication source which can authenticate against both SAML 2.0 and Shibboleth 1.3 IdPs.
  'simplesaml' => array(
		'saml:SP',
		'certificate' => 'cert.pem',
		'privatekey' => 'clave.pem',
    'privatekey_pass' => '',
		'entityID' => '',
		'idp' => 'idp',
		'discoURL' => null,
    'sign.authnrequest' => TRUE,
    'signature.algorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
    'AssertionConsumerServiceURL' => '',
    'ProviderName' => '',
    'ForceAuthn' => FALSE,
    'AttributeConsumingServiceIndex' => 0,
    'Consent' => 'urn:oasis:names:tc:SAML:2.0:consent:obtained',
    'sign.logout' => TRUE
  )
);
