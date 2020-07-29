<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

// -- METADADA DEL IDP
$metadata['idp'] = array (
  'entityid' => '',
  'metadata-set' => 'saml20-idp-remote',
  'redirect.sign' => true,
  'SingleSignOnService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/Shibboleth/SSO',
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML2/POST/SSO',
    ),
    2 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML2/POST-SimpleSign/SSO',
    ),
    3 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML2/Redirect/SSO',
    ),
  ),
  'SingleLogoutService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML2/Redirect/SLO',
    ),
  ),
  'ArtifactResolutionService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://auth-testing.iduruguay.gub.uy/v1.1/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'certFingerprint' =>
  array (
    0 => '2ea7eb54952c8112ede3749a14009066d49ddae2',
  ),
  'certData' =>'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',
  'scopes' =>
  array (
    0 => 'localhost',
  ),
);
