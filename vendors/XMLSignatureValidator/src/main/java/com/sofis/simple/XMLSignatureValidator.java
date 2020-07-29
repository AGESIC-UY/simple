/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.sofis.simple;

import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.nio.charset.Charset;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.security.cert.Certificate;
import java.security.cert.CertificateFactory;
import java.util.Properties;
import java.security.cert.X509Certificate;
import java.util.Arrays;
import java.util.List;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import org.apache.velocity.texen.util.FileUtil;
import org.bouncycastle.util.encoders.Base64;
import org.opensaml.Configuration;
import org.opensaml.DefaultBootstrap;
import org.opensaml.saml2.core.Assertion;
import org.opensaml.saml2.core.Response;
import org.opensaml.xml.XMLObject;
import org.opensaml.xml.io.Unmarshaller;
import org.opensaml.xml.io.UnmarshallerFactory;
import org.opensaml.xml.security.x509.BasicX509Credential;
import org.opensaml.xml.signature.Signature;
import org.opensaml.xml.signature.SignatureValidator;
import org.opensaml.xml.validation.ValidationException;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

/**
 *
 * @author bruno
 */
public class XMLSignatureValidator {

    private static Properties config;
    private static String path;

    public static void main(String[] args) {

       
        //File f =new File();
        try {
            // File f = new File("/tmp/xmlsigner.log");
            // f.createNewFile();
            load();

            String cert = args[0];
            String docu = args[1];

            //List<String> lines = Arrays.asList(cert, docu);
            //Path file = Paths.get("/tmp/xmlsigner.log");
            //Files.write(file, lines, Charset.forName("UTF-8"));

            cmdValidar(docu, cert);
        } catch (Exception ex) {
            System.err.println("ERR: " + ex.getMessage());
            ex.printStackTrace();
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

    private static Document loadXMLFromString(String xml) throws Exception {
        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
        factory.setNamespaceAware(true);
        DocumentBuilder builder = factory.newDocumentBuilder();
        return builder.parse(new ByteArrayInputStream(xml.getBytes()));
    }
}
