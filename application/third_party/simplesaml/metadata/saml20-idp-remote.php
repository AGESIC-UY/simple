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
  'entityid' => 'xxxx',
  'metadata-set' => 'saml20-idp-remote',
  'redirect.sign' => true,
  'SingleSignOnService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/Shibboleth/SSO',
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML2/POST/SSO',
    ),
    2 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML2/POST-SimpleSign/SSO',
    ),
    3 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML2/Redirect/SSO',
    ),
  ),
  'SingleLogoutService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML2/Redirect/SLO',
    ),
  ),
  'ArtifactResolutionService' =>
  array (
    0 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 =>
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://test-eid.portal.gub.uy/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'certFingerprint' =>
  array (
    0 => '2ea7eb54952c8112ede3749a14009066d49ddae2',
  ),
  'certData' => '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',
  'scopes' =>
  array (
    0 => 'localhost',
  ),
);
