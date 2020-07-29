package com.sofis.simple;

import java.io.ByteArrayInputStream;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.io.StringWriter;
import java.security.GeneralSecurityException;
import java.security.Key;
import java.security.KeyPair;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.NoSuchProviderException;
import java.security.PrivateKey;
import java.security.Provider;
import java.security.PublicKey;
import java.security.UnrecoverableKeyException;
import java.security.cert.Certificate;
import java.security.cert.CertificateFactory;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Enumeration;
import java.util.List;
import java.util.Properties;
import javax.xml.crypto.MarshalException;
import javax.xml.crypto.dsig.CanonicalizationMethod;
import javax.xml.crypto.dsig.DigestMethod;
import javax.xml.crypto.dsig.Reference;
import javax.xml.crypto.dsig.SignatureMethod;
import javax.xml.crypto.dsig.SignedInfo;
import javax.xml.crypto.dsig.Transform;
import javax.xml.crypto.dsig.XMLSignature;
import javax.xml.crypto.dsig.XMLSignatureException;
import javax.xml.crypto.dsig.XMLSignatureFactory;
import javax.xml.crypto.dsig.dom.DOMSignContext;
import javax.xml.crypto.dsig.keyinfo.KeyInfo;
import javax.xml.crypto.dsig.keyinfo.KeyInfoFactory;
import javax.xml.crypto.dsig.keyinfo.X509Data;
import javax.xml.crypto.dsig.spec.C14NMethodParameterSpec;
import javax.xml.crypto.dsig.spec.ExcC14NParameterSpec;
import javax.xml.crypto.dsig.spec.TransformParameterSpec;
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
import org.picketlink.identity.federation.core.saml.v2.util.DocumentUtil;
import org.w3c.dom.Attr;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;

public class XMLSignerOld {

  private static Properties config;
  private static String path;
  private static XMLSignatureFactory fac = getXMLSignatureFactory();

  public static void main(String[] args) {
    load();
    try {
      String cert = args[0];
      String pass = args[1];
      String docu = args[2];
      docu = new String(Base64.decode(docu));
      docu = docu.replace("\n", "").replace("\r", "");
      Document doc = loadXMLFromString(docu);
      doc = sign2(doc, cert, pass);
      System.out.println("OK: " + new String(Base64.encode(loadStringFromXML(doc).getBytes())));
    } catch (Exception ex) {
      System.err.println("ERR: " + ex.getMessage());
    }
  }

  private static void load() {
    try {
      config = new Properties();
      config.load(XMLSignatureValidator.class.getClassLoader().getResourceAsStream("config.properties"));
      path = config.getProperty("keystore_directory");
    } catch (IOException ex) {
      System.err.println(ex.getMessage());
    }
  }

  private static Object[] getKeyPair(String cert, String pass)
          throws NoSuchAlgorithmException, KeyStoreException, UnrecoverableKeyException, FileNotFoundException, IOException, java.security.cert.CertificateException, javax.security.cert.CertificateException {
    FileInputStream is = new FileInputStream(path + "/" + cert);
    KeyStore keystore = KeyStore.getInstance("PKCS12");
    keystore.load(is, pass.toCharArray());
    String alias = (String) keystore.aliases().nextElement();
    Key key = keystore.getKey(alias, pass.toCharArray());
    if ((key instanceof PrivateKey)) {
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

  private static Document loadXMLFromString(String xml)
          throws Exception {
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

  public static void load(String[] args) {
  }

  private static Document sign2(Document doc, String cert, String pass)
          throws Exception {
    Object[] data = getKeyPair(cert, pass);
    KeyPair kp = (KeyPair) data[0];
    X509Certificate x509cert = (X509Certificate) data[1];
    Element assertion = doc.getDocumentElement();
    Document tokenDocument = DocumentUtil.createDocument();
    assertion = (Element) tokenDocument.importNode(assertion, true);
    tokenDocument.appendChild(assertion);
    String assertionId = setupIDAttribute(assertion);
    tokenDocument = sign(tokenDocument, assertion, kp.getPrivate(), x509cert, "http://www.w3.org/2000/09/xmldsig#sha1", "http://www.w3.org/2000/09/xmldsig#rsa-sha1", assertionId);
    return tokenDocument;
  }

  public static Document sign(Document doc, Node nodeToBeSigned, PrivateKey signingKey, X509Certificate certificate, String digestMethod, String signatureMethod, String referenceURI)
          throws ParserConfigurationException, GeneralSecurityException, MarshalException, XMLSignatureException {
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
    if ((nodeToBeSigned.getLocalName().equals("Assertion"))
            && ("urn:oasis:names:tc:SAML:2.0:assertion"
            .equals(nodeToBeSigned
                    .getNamespaceURI()))) {
      Node signatureNode = DocumentUtil.getElement(newDoc, new QName("http://www.w3.org/2000/09/xmldsig#", "Signature"));
      Node subjectNode = DocumentUtil.getElement(newDoc, new QName("urn:oasis:names:tc:SAML:2.0:assertion", "Subject"));
      if ((signatureNode != null) && (subjectNode != null)) {
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

  public static Document sign(Document doc, PrivateKey signingKey, X509Certificate certificate, String digestMethod, String signatureMethod, String referenceURI) throws GeneralSecurityException, MarshalException, XMLSignatureException {
    DOMSignContext dsc = new DOMSignContext(signingKey, doc.getDocumentElement());
    signImpl(dsc, digestMethod, signatureMethod, referenceURI, certificate);
    return doc;
  }

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

  private static void signImpl(DOMSignContext dsc, String digestMethod, String signatureMethod, String referenceURI, X509Certificate certificate) throws GeneralSecurityException, MarshalException, XMLSignatureException {
    dsc.setDefaultNamespacePrefix("dsig");
    DigestMethod digestMethodObj = fac.newDigestMethod(digestMethod, null);
    Transform transform1 = fac.newTransform("http://www.w3.org/2000/09/xmldsig#enveloped-signature", (TransformParameterSpec) null);
    List prefixList = new ArrayList();
    prefixList.add("xs");
    TransformParameterSpec parameterSpec = new ExcC14NParameterSpec(prefixList);
    Transform transform2 = fac.newTransform("http://www.w3.org/2001/10/xml-exc-c14n#", parameterSpec);
    List transformList = new ArrayList();
    transformList.add(transform1);
    transformList.add(transform2);
    Reference ref = fac.newReference(referenceURI, digestMethodObj, transformList, null, null);
    CanonicalizationMethod canonicalizationMethod = fac.newCanonicalizationMethod("http://www.w3.org/2001/10/xml-exc-c14n#", (C14NMethodParameterSpec) null);
    List referenceList = Collections.singletonList(ref);
    SignatureMethod signatureMethodObj = fac.newSignatureMethod(signatureMethod, null);
    SignedInfo si = fac.newSignedInfo(canonicalizationMethod, signatureMethodObj, referenceList);
    KeyInfoFactory kif = fac.getKeyInfoFactory();
    X509Data x509Data = kif.newX509Data(Collections.singletonList(certificate));
    KeyInfo ki = kif.newKeyInfo(Collections.singletonList(x509Data));
    XMLSignature signature = fac.newXMLSignature(si, ki);
    signature.sign(dsc);
  }

  private static String setupIDAttribute(Node node) {
    if ((node instanceof Element)) {
      Element assertion = (Element) node;
      if (assertion.getLocalName().equals("Assertion")) {
        if ((assertion.getNamespaceURI().equals("urn:oasis:names:tc:SAML:2.0:assertion")) && (assertion.hasAttribute("ID"))) {
          assertion.setIdAttribute("ID", true);
          return "#" + assertion.getAttribute("ID");
        }
        if ((assertion.getNamespaceURI().equals("urn:oasis:names:tc:SAML:1.0:assertion"))
                && (assertion
                .hasAttribute("AssertionID"))) {
          assertion.setIdAttribute("AssertionID", true);
          return "#" + assertion.getAttribute("AssertionID");
        }
      }
    }
    return "";
  }
}
