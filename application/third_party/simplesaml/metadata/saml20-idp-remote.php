<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */
$metadata['https://openidp.feide.no'] = array(
	'name' => array(
		'en' => 'Feide OpenIdP - guest users',
		'no' => 'Feide Gjestebrukere',
	),
	'description'          => 'Here you can login with your account on Feide RnD OpenID. If you do not already have an account on this identity provider, you can create a new one by following the create new account link and follow the instructions.',
	'SingleSignOnService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'c9ed4dfb07caf13fc21e0fec1572047eb8a7a4cb'
);

$metadata['test-idp.portal.gub.uy'] = array (
  'entityid' => 'idp.portal.gub.uy',
	'ForceAuthn' =>	'false',
  'contacts' =>
  array (
  ),
  'metadata-set' => 'shib13-idp-remote',
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
  'keys' =>
  array (
    0 =>
    array (
      'encryption' => true,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIIGHDCCBASgAwIBAgITWpSU9VDoCZ1hch8amQtHPBGbDTANBgkqhkiG9w0BAQsFADBaMR0wGwYD
VQQDExRDb3JyZW8gVXJ1Z3VheW8gLSBDQTEsMCoGA1UECgwjQWRtaW5pc3RyYWNpw7NuIE5hY2lv
bmFsIGRlIENvcnJlb3MxCzAJBgNVBAYTAlVZMB4XDTE1MDUxOTIxMjcxMVoXDTE3MDUxODIxMjcx
MVowSTEYMBYGA1UEBRMPUlVDMjE1OTk2MDYwMDE1MQswCQYDVQQGEwJVWTEPMA0GA1UEChMGQUdF
U0lDMQ8wDQYDVQQDEwZBR0VTSUMwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC2FqZZ
WAu3HZFmdKpg4JJyNxLB3KQhBZl2FXbAVHINF8TS7cAhRv9bvHCZCHQg/fTnWNhuLecsMxZq0KTz
0KdhwmGqK303rmK7Sovig8LiXK8tqKUAUFmiUNKKKRjav/JFgbLtZwHQ0yGRtSa4iLcMEWCgDw4+
9YuYcQp0iKvWOQI+zXbkCsGxAxlk/A+3sF5tDS1crtVx+4x2Z0PdyTJ7bwBmhwv2hGlAV18CsmyB
Q8Kuj9HeF00G4ZNjcemHZtGq9/pESZ+sGij8bzv97G19ATbbhHpVFti+iDTooY4iu/uDXuxoG+Bh
TVWSNFXKFWj9s5NnJRFUU8S0Q+r8u17PAgMBAAGjggHqMIIB5jB5BggrBgEFBQcBAQRtMGswNgYI
KwYBBQUHMAKGKmh0dHA6Ly9hbmNjYS5jb3JyZW8uY29tLnV5L2FuY2NhL2FuY2NhLmNlcjAxBggr
BgEFBQcwAYYlaHR0cDovL2FuY2NhLmNvcnJlby5jb20udXkvYW5jY2EvT0NTUDAOBgNVHQ8BAf8E
BAMCBPAwDAYDVR0TAQH/BAIwADA7BgNVHR8ENDAyMDCgLqAshipodHRwOi8vYW5jY2EuY29ycmVv
LmNvbS51eS9hbmNjYS9hbmNjYS5jcmwwgbgGA1UdIASBsDCBrTBkBgtghlqE4q4dhIgFBDBVMFMG
CCsGAQUFBwIBFkdodHRwOi8vdWNlLmd1Yi51eS9pbmZvcm1hY2lvbi10ZWNuaWNhL3BvbGl0aWNh
cy9jcF9wZXJzb25hX2p1cmlkaWNhLnBkZjBFBgtghlqE4q4dhIgFBjA2MDQGCCsGAQUFBwIBFiho
dHRwOi8vYW5jY2EuY29ycmVvLmNvbS51eS9hbmNjYS9jcHMucGRmMBMGA1UdJQQMMAoGCCsGAQUF
BwMCMB0GA1UdDgQWBBR2VEkVIxviZEZzsAHExTk4EjSsOTAfBgNVHSMEGDAWgBRs4rAmjVvWJggf
mF1p4A5/VeyudjANBgkqhkiG9w0BAQsFAAOCAgEAQgw+tG4rWknxgG1p+ax+DQuY2Qoq0KIkkfU0
8m+o+ZoHWSx7uzF4vx1i1zoavbcU2Wx8NkjyPHKLtgokl0r60llE09f7fDfI2Nqe10cKJqNrNgJB
Ooa3ZCdYeU7Wsu02XUjSydHHz5rEyROgHEpaGo0OKNIxUlkNpyYK9gBsFt1epHPf1VkDkd0roFEt
ZSKfxienm9GD7vZjn8Xb+R37F9u2QwlzCbtifmEtSkDnDXJ8uB/ajyrvmC/iaRjifptixd50IiV+
7gYmj1eVjCzv4azA6EcWunwfngcAlcQTtr0V1ffcw0J0uZtX9NUKX+6ggwddgEKp+0gVmtKDaUdt
wJHW2YaGkEYQDdidBoaKxLAmOm3DJ5G9RuO4UDHdx8sK/b854aaoCYKH3+TDaJeyYbij6YiNycxI
2qvEczFbUSByUogBxQEV2S4t3iKIbZH1fVzwEX08Q0uQG3aEEwDaAkYVh1eD6/Nae4oRDK3j7lhq
/T2ro0v329p93zly3+XEOGQxoXBeol4cSIUhQjfZN2kJ/W6V9u9OasWr/whPdZr4wv5yVaRPnYEP
4DhNXXapCptYnlG8FBWbi0a1rm6gF0Dv1qwsF/4ixVxFO8p8anlHyHIS71ZAV2WqCGBy7FFohgLm
rZUMaZ5CbyTGihfFm0eRAFseWZ78beO6zHmgXNA=
',
    ),
  ),
  'scope' =>
  array (
    0 => 'localhost',
  ),
);
