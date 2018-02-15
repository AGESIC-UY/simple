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
  'entityid' => 'http://simple.xxx.com.uy/sp',
  'metadata-set' => 'saml20-idp-remote',
  'redirect.sign' => true,
  'SingleSignOnService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/Shibboleth/SSO',
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML2/POST/SSO',
    ),
    2 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML2/POST-SimpleSign/SSO',
    ),
    3 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML2/Redirect/SSO',
    ),
  ),
  'SingleLogoutService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML2/Redirect/SLO',
    ),
  ),
  'ArtifactResolutionService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://test-eid.portal.gub.uy/v1.1/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'certFingerprint' =>
  array (
    0 => '2ea7eb54952c8112ede3749a14009066d49ddae2',
  ),
  'certData' =>'XXX',
  'scopes' =>
  array (
    0 => 'localhost',
  ),
);
