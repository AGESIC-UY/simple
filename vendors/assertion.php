<?php

$service = 'http://testservicios.pge.red.uy/WsCertif/wsCertif';
$uuid = mt_rand();
$policyName = 'urn:tokensimple';
$role = 'ou=gerencia de proyectos,o=agesic';

$soapMessagePartOne = "<s:Envelope xmlns:a=\"http://www.w3.org/2005/08/addressing\" xmlns:saml=\"urn:oasis:names:tc:SAML:2.0:assertion\" xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">";
$soapMessagePartOne .= "<s:Header><a:Action s:mustUnderstand=\"1\">http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</a:Action>";
$soapMessagePartOne .= "<a:MessageID>urn:uuid:";
$soapMessagePartOne .= $uuid;
$soapMessagePartOne .= "</a:MessageID>";
$soapMessagePartOne .= "</s:Header><s:Body><wst:RequestSecurityToken xmlns:wst=\"http://schemas.xmlsoap.org/ws/2005/02/trust\">";
$soapMessagePartOne .= "<wst:TokenType>http://docs.oasis-open.org/wss/oasis-wss-saml-token-profile-1.1#SAMLV1.1</wst:TokenType>";
$soapMessagePartOne .= "<wst:AppliesTo xmlns=\"http://schemas.xmlsoap.org/ws/2004/09/policy\"><a:EndpointReference>";
$soapMessagePartOne .= "<a:Address>";
$soapMessagePartOne .= $service;
$soapMessagePartOne .= "</a:Address></a:EndpointReference></wst:AppliesTo>";
$soapMessagePartOne .= "<wst:RequestType>http://schemas.xmlsoap.org/ws/2005/02/trust/Issue</wst:RequestType><wst:Issuer>";
$soapMessagePartOne .= "<a:Address>" . $policyName . "</a:Address></wst:Issuer><wst:Base>";

$date = new DateTime(date("Y-m-d\TH:i:s"));
$date->add(new DateInterval('PT30M')); // -- 30 minutes
$future_date = $date->format("Y-m-d\TH:i:s.000");

$assertion = '<saml:Assertion xmlns="urn:oasis:names:tc:SAML:1.0:assertion" xmlns:saml="urn:oasis:names:tc:SAML:1.0:assertion" IssueInstant="'. date("Y-m-d\TH:i:s.000") .'Z" Issuer="Agesic" MajorVersion="1" MinorVersion="1"><saml:Conditions NotBefore="'. date("Y-m-d\TH:i:s.000") .'Z" NotOnOrAfter="'. $future_date .'Z"/><saml:AuthenticationStatement AuthenticationInstant="'. date("Y-m-d\TH:i:s.000O") .'" AuthenticationMethod="urn:oasis:names:tc:SAML:1.0:am:password"><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">ou=gerencia de proyectos,o=agesic</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject></saml:AuthenticationStatement><saml:AttributeStatement><saml:Subject><saml:NameIdentifier Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">ou=gerencia de proyectos,o=agesic</saml:NameIdentifier><saml:SubjectConfirmation><saml:ConfirmationMethod>urn:oasis:names:tc:SAML:1.0:cm:bearer</saml:ConfirmationMethod></saml:SubjectConfirmation></saml:Subject><saml:Attribute AttributeName="User" AttributeNamespace="urn:tokensimple"><saml:AttributeValue xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="xs:string"/></saml:Attribute></saml:AttributeStatement></saml:Assertion>';

$assertion_base64 = base64_encode($assertion);
$raw_signed_assertion = exec("java -jar xmlsigner.jar bruno bruno $assertion_base64 2>&1");

preg_match('/^ERR:/', $raw_signed_assertion, $match);
if(!empty($match)) {
  echo str_replace('ERR: ', '', $raw_signed_assertion) . "\n";
  return false;
}

$signed_assertion = base64_decode(str_replace('OK: ', '', $raw_signed_assertion));

$signed_assertion = str_replace('<?xml version="1.0"?>', '', $signed_assertion);
$soapMessagePartTwo = "</wst:Base><wst:SecondaryParameters><wst:Rol>";
$soapMessagePartTwo .= $role;
$soapMessagePartTwo .= "</wst:Rol></wst:SecondaryParameters></wst:RequestSecurityToken></s:Body></s:Envelope>";

$body = $soapMessagePartOne . $signed_assertion . $soapMessagePartTwo;

header('Content-Type: text/xml');

$header = array("Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "Content-length: " . strlen($body));

$soap_do = curl_init();
curl_setopt($soap_do, CURLOPT_URL, 'http://testservicios.pge.red.uy:6001/TrustServer/SecurityTokenService');
curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($soap_do, CURLOPT_TIMEOUT,        120);

// -- PROXY only for test
curl_setopt($soap_do, CURLOPT_PROXY, '192.168.1.141:808');
curl_setopt($soap_do, CURLOPT_FOLLOWLOCATION, 1);

curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($soap_do, CURLOPT_POST,           true);
curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $body);
curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);

$soap_response = curl_exec($soap_do);
$curl_errno = curl_errno($soap_do);
$curl_error = curl_error($soap_do);
$http_code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);

curl_close($soap_do);

if(!empty($curl_error)) {
  echo $http_code . ' - ' . $curl_errno . ' - ' . $curl_error . "\n";
}

echo $soap_response;
