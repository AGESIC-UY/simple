/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.sofis.simple;

import java.io.ByteArrayInputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.StringWriter;
import java.security.GeneralSecurityException;
import java.security.Key;
import java.security.KeyPair;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.NoSuchProviderException;
import java.security.PrivateKey;
import java.security.PublicKey;
import java.security.UnrecoverableKeyException;
import java.security.cert.Certificate;
import java.security.cert.CertificateException;
import java.security.cert.CertificateFactory;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Properties;
import java.security.cert.X509Certificate;
import javax.xml.crypto.dsig.CanonicalizationMethod;
import javax.xml.crypto.dsig.DigestMethod;
import javax.xml.crypto.dsig.SignatureMethod;
import javax.xml.crypto.dsig.SignedInfo;
import javax.xml.crypto.dsig.Transform;
import javax.xml.crypto.dsig.XMLSignatureFactory;
import javax.xml.crypto.dsig.spec.C14NMethodParameterSpec;
import javax.xml.crypto.dsig.spec.TransformParameterSpec;
import javax.xml.crypto.dsig.Reference;
import javax.xml.crypto.dsig.XMLSignature;
import javax.xml.crypto.dsig.XMLSignatureException;
import javax.xml.crypto.dsig.dom.DOMSignContext;
import javax.xml.crypto.dsig.keyinfo.KeyInfo;
import javax.xml.crypto.dsig.keyinfo.KeyInfoFactory;
import javax.xml.crypto.dsig.keyinfo.X509Data;
import javax.xml.crypto.dsig.spec.ExcC14NParameterSpec;
import javax.xml.namespace.QName;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.bouncycastle.util.encoders.Base64;
//import org.opensaml.Configuration;
//import org.opensaml.DefaultBootstrap;
//import org.opensaml.saml2.core.Assertion;
//import org.opensaml.saml2.core.Response;
//import org.opensaml.xml.XMLObject;
//import org.opensaml.xml.io.Unmarshaller;
//import org.opensaml.xml.io.UnmarshallerFactory;
//import org.opensaml.xml.security.x509.BasicX509Credential;
//import org.opensaml.xml.signature.Signature;
//import org.opensaml.xml.signature.SignatureValidator;
//import org.opensaml.xml.validation.ValidationException;
import org.picketlink.identity.federation.core.saml.v1.SAML11Constants;
import org.picketlink.identity.federation.core.saml.v2.util.DocumentUtil;
import org.picketlink.identity.federation.core.wstrust.WSTrustConstants;
import org.w3c.dom.Attr;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

/**
 *
 * @author bruno
 */
public class XMLSigner {

  private static Properties config;
  private static String path;

  public static void main(String[] args) {

    load();

    try {

        String cert = args[1];
        String pass = args[2];
        String docu = args[3];
        
        //System.out.println("Antes de firmar: cert="+cert);
        //System.out.println("Antes de firmar: pass="+pass);
        //System.out.println("Antes de firmar: docu="+docu);
        
        cmdFirmar(docu, cert, pass);
      
        //System.out.println("Despues de firmar");

/*      
      String cmd = args[0];
      if ("firmar".equals(cmd)) {
        String cert = args[1];
        String pass = args[2];
        String docu = args[3];
        cmdFirmar(docu, cert, pass);
      }
      if ("validar".equals(cmd)) {
        String cert = args[1];
        String docu = args[2];
        cmdValidar(docu, cert);
      }
*/
    } catch (Exception ex) {
      System.err.println("ERR: " + ex.getMessage());
      ex.printStackTrace();
    }

  }

  private static void load() {
    try {
      config = new Properties();
      config.load(XMLSigner.class.getClassLoader().getResourceAsStream("config.properties"));
      path = config.getProperty("keystore_directory");
    } catch (IOException ex) {
      System.err.println(ex.getMessage());
    }
  }

  private static void cmdFirmar(String docu, String cert, String pass) throws Exception {
    docu = new String(Base64.decode(docu));
    docu = docu.replace("\n", "").replace("\r", "");
    Document doc = loadXMLFromString(docu);
    doc = sign2(doc, cert, pass);
    System.out.println("OK: " + new String(Base64.encode(loadStringFromXML(doc).getBytes())));
    //System.out.println("Firmado: " + loadStringFromXML(doc));
  }
/*
  private static void cmdValidar(String docu, String cert) throws Exception {

    DefaultBootstrap.bootstrap();

    docu = new String(Base64.decode(docu));
    docu = docu.replace("\n", "").replace("\r", "");

    Document doc = loadXMLFromString(docu);
    Element element = doc.getDocumentElement();
    NodeList assertionElements = element.getElementsByTagNameNS("urn:oasis:names:tc:SAML:2.0:assertion", "Assertion");
    for (int i = 0; i < assertionElements.getLength(); i++) {
      Element assertionElement = (Element) assertionElements.item(i);
      assertionElement.setIdAttributeNS(null, "ID", true);
    }
    UnmarshallerFactory unmarshallerFactory = Configuration.getUnmarshallerFactory();
    Unmarshaller unmarshaller = unmarshallerFactory.getUnmarshaller(element);
    XMLObject responseXmlObj = unmarshaller.unmarshall(element);
    Response samlResponse = (Response) responseXmlObj;
    Assertion assertion = samlResponse.getAssertions().get(0);
    Signature signature = assertion.getSignature();

    //Cargar el certificado
    FileInputStream inStream = new FileInputStream(path + "/" + cert);
    CertificateFactory cf = CertificateFactory.getInstance("X.509");
    Certificate cert1 = cf.generateCertificate(inStream);
    inStream.close();
    BasicX509Credential credencial = new BasicX509Credential();
    credencial.setEntityCertificate((X509Certificate) cert1);
    credencial.setPublicKey(cert1.getPublicKey());
    credencial.setPrivateKey(null);
    
    boolean firmaValida;
    try {
      SignatureValidator signatureValidator = new SignatureValidator(credencial);
      signatureValidator.validate(signature);
      firmaValida = true;
    } catch (ValidationException ex) {
      firmaValida = false;
      ex.printStackTrace();
    }

    if (firmaValida) {
      System.out.println("OK");
    } else {
      System.out.println("NOK");
    }

  }
*/
  private static Object[] getKeyPair(String cert, String pass) throws NoSuchAlgorithmException, KeyStoreException, UnrecoverableKeyException, FileNotFoundException, IOException, CertificateException, javax.security.cert.CertificateException {
    FileInputStream is = new FileInputStream(path + "/" + cert);
    KeyStore keystore = KeyStore.getInstance("PKCS12");
    keystore.load(is, pass.toCharArray());
    String alias = keystore.aliases().nextElement();
    Key key = keystore.getKey(alias, pass.toCharArray());
    if (key instanceof PrivateKey) {
      Certificate certi = keystore.getCertificate(alias);
      PublicKey publicKey = certi.getPublicKey();
      CertificateFactory cf = CertificateFactory.getInstance("X.509");
      ByteArrayInputStream bais = new ByteArrayInputStream(certi.getEncoded());
      X509Certificate x509cert = (X509Certificate) cf.generateCertificate(bais);
      Object[] ret = new Object[2];
      ret[0] = new KeyPair(publicKey, (PrivateKey) key);
      ret[1] = x509cert;
      return ret;
    }
    return null;
  }

  private static Document loadXMLFromString(String xml) throws Exception {
    DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
    factory.setNamespaceAware(true);
    DocumentBuilder builder = factory.newDocumentBuilder();
    return builder.parse(new ByteArrayInputStream(xml.getBytes()));
  }

  private static String loadStringFromXML(Document doc) throws ParserConfigurationException, TransformerConfigurationException, TransformerException {

    DocumentBuilderFactory domFact = DocumentBuilderFactory.newInstance();
    DocumentBuilder builder = domFact.newDocumentBuilder();
    DOMSource domSource = new DOMSource(doc);
    StringWriter writer = new StringWriter();
    StreamResult result = new StreamResult(writer);
    TransformerFactory tf = TransformerFactory.newInstance();
    Transformer transformer = tf.newTransformer();
    transformer.transform(domSource, result);
    return writer.toString();
  }

  private static Document sign2(Document doc, String cert, String pass) throws Exception {
    Object[] data = getKeyPair(cert, pass);
    KeyPair kp = (KeyPair) data[0];
    X509Certificate x509cert = (X509Certificate) data[1];
    Element assertion = doc.getDocumentElement();
    Document tokenDocument = DocumentUtil.createDocument();
    assertion = (Element) tokenDocument.importNode(assertion, true);
    tokenDocument.appendChild(assertion);
    String assertionId = setupIDAttribute(assertion);
    tokenDocument = sign(tokenDocument, assertion, kp.getPrivate(), x509cert, DigestMethod.SHA1, SignatureMethod.RSA_SHA1, assertionId);
    return tokenDocument;
  }

  public static Document sign(Document doc, Node nodeToBeSigned, PrivateKey signingKey, X509Certificate certificate, String digestMethod,
          String signatureMethod, String referenceURI) throws ParserConfigurationException, GeneralSecurityException,
          javax.xml.crypto.MarshalException, XMLSignatureException {
    if (nodeToBeSigned == null) {
      throw new IllegalArgumentException("Node to be signed");
    }
    Node parentNode = nodeToBeSigned.getParentNode();
    Document newDoc = DocumentUtil.createDocument();
    Node signingNode = newDoc.importNode(nodeToBeSigned, true);
    newDoc.appendChild(signingNode);
    if (!referenceURI.isEmpty()) {
      propagateIDAttributeSetup(nodeToBeSigned, newDoc.getDocumentElement());
    }
    newDoc = sign(newDoc, signingKey, certificate, digestMethod, signatureMethod, referenceURI);
    if (nodeToBeSigned.getLocalName().equals("Assertion")
            && WSTrustConstants.SAML2_ASSERTION_NS.equals(nodeToBeSigned.getNamespaceURI())) {
      Node signatureNode = DocumentUtil.getElement(newDoc, new QName(WSTrustConstants.DSIG_NS, "Signature"));
      Node subjectNode = DocumentUtil.getElement(newDoc, new QName(WSTrustConstants.SAML2_ASSERTION_NS, "Subject"));
      if (signatureNode != null && subjectNode != null) {
        newDoc.getDocumentElement().removeChild(signatureNode);
        newDoc.getDocumentElement().insertBefore(signatureNode, subjectNode);
      }
    }
    Node signedNode = doc.importNode(newDoc.getFirstChild(), true);
    if (!referenceURI.isEmpty()) {
      propagateIDAttributeSetup(newDoc.getDocumentElement(), (Element) signedNode);
    }
    parentNode.replaceChild(signedNode, nodeToBeSigned);
    return doc;
  }

  public static void propagateIDAttributeSetup(Node sourceNode, Element destElement) {
    NamedNodeMap nnm = sourceNode.getAttributes();
    for (int i = 0; i < nnm.getLength(); i++) {
      Attr attr = (Attr) nnm.item(i);
      if (attr.isId()) {
        destElement.setIdAttribute(attr.getName(), true);
        break;
      }
    }
  }

  public static Document sign(Document doc, PrivateKey signingKey, X509Certificate certificate, String digestMethod, String signatureMethod, String referenceURI)
          throws GeneralSecurityException, javax.xml.crypto.MarshalException, XMLSignatureException {
    DOMSignContext dsc = new DOMSignContext(signingKey, doc.getDocumentElement());
    signImpl(dsc, digestMethod, signatureMethod, referenceURI, certificate);
    return doc;
  }

  private static final XMLSignatureFactory fac = getXMLSignatureFactory();

  private static XMLSignatureFactory getXMLSignatureFactory() {
    XMLSignatureFactory xsf = null;
    try {
      xsf = XMLSignatureFactory.getInstance("DOM", "ApacheXMLDSig");
    } catch (NoSuchProviderException ex) {
      try {
        xsf = XMLSignatureFactory.getInstance("DOM");
      } catch (Exception err) {
        throw new RuntimeException(err);
      }
    }
    return xsf;
  }

  private static void signImpl(DOMSignContext dsc, String digestMethod, String signatureMethod, String referenceURI, X509Certificate certificate)
          throws GeneralSecurityException, javax.xml.crypto.MarshalException, XMLSignatureException {
    dsc.setDefaultNamespacePrefix("dsig");
    DigestMethod digestMethodObj = fac.newDigestMethod(digestMethod, null);
    Transform transform1 = fac.newTransform(Transform.ENVELOPED, (TransformParameterSpec) null);
    List<String> prefixList = new ArrayList<>();
    prefixList.add("xs");
    TransformParameterSpec parameterSpec = new ExcC14NParameterSpec(prefixList);
    Transform transform2 = fac.newTransform("http://www.w3.org/2001/10/xml-exc-c14n#", parameterSpec);
    List<Transform> transformList = new ArrayList<>();
    transformList.add(transform1);
    transformList.add(transform2);
    Reference ref = fac.newReference(referenceURI, digestMethodObj, transformList, null, null);
    CanonicalizationMethod canonicalizationMethod = fac.newCanonicalizationMethod(CanonicalizationMethod.EXCLUSIVE, (C14NMethodParameterSpec) null);
    List<Reference> referenceList = Collections.singletonList(ref);
    SignatureMethod signatureMethodObj = fac.newSignatureMethod(signatureMethod, null);
    SignedInfo si = fac.newSignedInfo(canonicalizationMethod, signatureMethodObj, referenceList);
    KeyInfoFactory kif = fac.getKeyInfoFactory();
    X509Data x509Data = kif.newX509Data(Collections.singletonList(certificate));
    KeyInfo ki = kif.newKeyInfo(Collections.singletonList(x509Data));
    XMLSignature signature = fac.newXMLSignature(si, ki);
    signature.sign(dsc);
  }

  private static String setupIDAttribute(Node node) {
    if (node instanceof Element) {
      Element assertion = (Element) node;
      if (assertion.getLocalName().equals("Assertion")) {
        if (assertion.getNamespaceURI().equals(WSTrustConstants.SAML2_ASSERTION_NS) && assertion.hasAttribute("ID")) {
          assertion.setIdAttribute("ID", true);
          return "#" + assertion.getAttribute("ID");
        } else if (assertion.getNamespaceURI().equals(SAML11Constants.ASSERTION_11_NSURI)
                && assertion.hasAttribute(SAML11Constants.ASSERTIONID)) {
          assertion.setIdAttribute(SAML11Constants.ASSERTIONID, true);
          return "#" + assertion.getAttribute(SAML11Constants.ASSERTIONID);
        }
      }
    }
    return "";
  }

}
