<?php

namespace Piloto;

error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 'On');

use DOMdocument;
use Piloto\Contracts\CfdiTypeInterface;
use Piloto\Exceptions\CfdiException;
use XSLTProcessor;
use SimpleXMLElement;
use Piloto\DOMValidator;

// al igual que las etiquetas el XML se ordenan las clases , para que aparezca en ese mismo
// orden en el XML.

class Cfdi {

    protected $xml;
    protected $comprobante;
    protected $emisor;
    protected $prueba;
//protected $domicilioFiscal;
//protected $regimen;
    protected $retencion;
    protected $traslado;
    protected $receptor;
    protected $impuestos;
    protected $conceptos;
    protected $impuesto; // nuevo
    protected $traslados;
    protected $retenciones;
    protected $totalImpuestosTrasladados = 0;
    protected $totalImpuestosRetenidos = 0;
    protected $urlToFile = "file:///var/www/html/pacV33/vendor/Piloto/cfdi/src/Piloto/xslt/cadenaoriginal_3_3.xslt"; // ?
    //protected $urlToFile = "http://www.sat.gob.mx/sitio_internet/cfd/3/cadenaoriginal_3_3/cadenaoriginal_3_3.xslt"; // ?
    protected $cadena_original;
    protected $sello;
    protected $certificado;
    protected $cer;
    protected $key;

// inicia la construccion del XML base

    public function __construct(CfdiTypeInterface $type, $cer, $key, $charset = "UTF-8") {

        $this->xml = new DOMdocument("1.0", $charset);
        $this->comprobante = $this->xml->appendChild(
                $this->xml->createElement('cfdi:Comprobante')
        );
        $this->setAttribute($this->comprobante, $type->getAttributes());

        $this->cer = $cer;
        $this->key = $key;
    }

    protected function setAttribute($nodo, $attributes) {
        foreach ($attributes as $key => $val) {
            $val = preg_replace('/\s\s+/', ' ', $val);
            $val = trim($val);
            if (strlen($val) > 0) {
                $val = utf8_encode(str_replace("|", "/", $val));
                $nodo->setAttribute($key, $val);
            }
        }
    }

// aqui añade el metodo y lo ejecuta debe llevar el mismo nombre al parecer
    public function add($data, $nodo = null) {
        $method = 'add' . $this->prepareMethod(get_class($data));
        try {
            $this->{$method}($data, $nodo);
        } catch (CfdiException $e) {
            return $e->getMessage();
        }
        return $data;
    }

    protected function prepareMethod($class) {
        $object = explode('\\', $class);
        return $object[1];
    }

    protected function addGeneralData($data, $nodo) {
        $this->setAttribute($this->comprobante, $data->getData());
    }

    protected function addEmisorData($data, $nodo) {
        $this->emisor = $this->comprobante->appendChild($this->xml->createElement("cfdi:Emisor"));
        $this->setAttribute($this->emisor, $data->getData());
    }

    /*
      protected function addDomicilioFiscalData($data, $nodo) {
      $element = $nodo == 'emisor' ? 'DomicilioFiscal' : 'Domicilio';
      $this->domicilioFiscal = $this->{$nodo}->appendChild($this->xml->createElement("cfdi:{$element}"));
      $this->setAttribute($this->domicilioFiscal, $data->getData());
      }
     */
    /*
      protected function addRegimenFiscalData($data, $nodo)
      {
      $this->regimen = $this->emisor->appendChild($this->xml->createElement("cfdi:RegimenFiscal"));
      $this->setAttribute($this->regimen, $data->getData());
      }
     */

    protected function addReceptorData($data, $nodo) {
        $this->receptor = $this->comprobante->appendChild($this->xml->createElement("cfdi:Receptor"));
        $this->setAttribute($this->receptor, $data->getData());
    }

    protected function addConceptosData($data, $nodo) {
        if (is_null($this->conceptos)) {
            $this->conceptos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Conceptos"));
        }

        $this->concepto = $this->conceptos->appendChild($this->xml->createElement("cfdi:Concepto"));
        $this->setAttribute($this->concepto, $data->getData());
    }

    // se debe de declarar una clase por nodo que tenga contenido conforme al documento de la v33 del SAT cada impuesto debe contener su subnodo 
    // y sus correspondientes subimpuestos

    /*
     *  modificación de la clase de impuestos, se añadieron 2 clases extra por ahora que contienen el valor de los impuestos por nodo
     *  con el valor del nodo correspondiente se ordenara en el xml los impuestos correspondientes en su concepto , funciona en base
     *  al valor del nodo que se declara en el add de las clases de traslado y retencion.
     */



    protected function addRetencionData($data, $nodo) {
        (array) $array = json_decode(json_encode($data->getData()), true);
        $indice = $nodo;
        if (is_null($this->$indice)) {
            $parent = $this->xml->getElementsByTagName('cfdi:Concepto')->item($nodo);
            $child = $this->xml->createElement("cfdi:Impuestos");
            $this->$indice = $parent->appendChild($child);
        }
        if (!isset($this->$indice->concepto_retencion)) {
            $this->$indice->concepto_retencion = $this->$indice->appendChild($this->xml->createElement("cfdi:Retenciones"));
        }
        $this->$indice->retencion = $this->$indice->concepto_retencion->appendChild($this->xml->createElement("cfdi:Retencion"));
        $this->setAttribute($this->$indice->retencion, $array);
    }

    protected function addTrasladoData($data, $nodo) {
        //print_r($data);
        (array) $array = json_decode(json_encode($data->getData()), true);
        $indice = $nodo;
        if (is_null($this->$indice)) {
            $parent = $this->xml->getElementsByTagName('cfdi:Concepto')->item($nodo);
            $child = $this->xml->createElement("cfdi:Impuestos");
            $this->$indice = $parent->appendChild($child);
            // $this->impuesto = $parent->appendChild($this->xml->createElement("cfdi:Impuestos"));
        }
        if (!isset($this->$indice->concepto_traslado)) {
            $this->$indice->concepto_traslado = $this->$indice->appendChild($this->xml->createElement("cfdi:Traslados"));
        }
        $this->$indice->traslado = $this->$indice->concepto_traslado->appendChild($this->xml->createElement("cfdi:Traslado"));
        $this->setAttribute($this->$indice->traslado, $array);
    }

    protected function addImpuestosRetenidosData($data, $nodo) {
        if (is_null($this->impuestos)) {
            $this->impuestos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Impuestos"));
        }

        if (is_null($this->retenciones)) {
            $this->retenciones = $this->impuestos->appendChild($this->xml->createElement("cfdi:Retenciones"));
        }

        $retencion = $this->retenciones->appendChild($this->xml->createElement("cfdi:Retencion"));
        $this->setAttribute($retencion, $data->getData());

        $this->totalImpuestosRetenidos += $retencion->getAttribute('Importe');

        $this->setAttribute($this->impuestos, [
            "totalImpuestosRetenidos" => number_format($this->totalImpuestosRetenidos, 2, '.', ''),
        ]);
    }

    protected function addImpuestosTrasladadosData($data, $nodo) {

        if (is_null($this->impuestos)) {
            $this->impuestos = $this->comprobante->appendChild($this->xml->createElement("cfdi:Impuestos"));
        }

        if (is_null($this->traslados)) {
            $this->traslados = $this->impuestos->appendChild($this->xml->createElement("cfdi:Traslados"));
        }

        $traslado = $this->traslados->appendChild($this->xml->createElement("cfdi:Traslado"));
        $this->setAttribute($traslado, $data->getData());

        $this->totalImpuestosTrasladados += $traslado->getAttribute('Importe');

        $this->setAttribute($this->impuestos, [
            "TotalImpuestosTrasladados" => number_format($this->totalImpuestosTrasladados, 2, '.', ''),
        ]);
    }

    public function getCadenaOriginal() {
        if (!is_null($this->cadena_original)) {
            return $this->cadena_original;
        }
echo $path = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];

        $xsl = new DOMDocument;
        $xsl->load($this->urlToFile);
        $procesador = new XSLTProcessor;
        $procesador->importStyleSheet($xsl);
        $paso = new DOMDocument;
        $paso->loadXML($this->xml());
        echo '<br>'.($this->cadena_original = $procesador->transformToXML($paso)).'<br>';
    }

// cambiar de sha1 a sha256 el sello
    protected function setSello() {
        $pkeyid = openssl_get_privatekey(file_get_contents($this->key));
        openssl_sign($this->getCadenaOriginal(), $crypttext, $pkeyid, OPENSSL_ALGO_SHA256); // convierte la cadena a sha256
        openssl_free_key($pkeyid); //libera la clave asociada con el indetificador de clave
        // ******** VERIFICAR QUE ENCRIPTE EL SELLO A SHA2 256 ****************++

        $this->comprobante->setAttribute("Sello", $this->sello = hash('sha256', $crypttext));

        $this->comprobante->setAttribute("Certificado", $this->getCertificado());
    }

    public function getCertificado() {
        if (!is_null($this->certificado)) {
            return $this->certificado;
        }

        return $this->parseCertificado();
    }

    public function getSello() {
        return $this->sello;
    }

    protected function parseCertificado() {
        $datos = file($this->cer);
        for ($i = 0; $i < sizeof($datos); $i++) {
            if (strstr($datos[$i], "END CERTIFICATE") || strstr($datos[$i], "BEGIN CERTIFICATE")) {
                continue;
            }
            $this->certificado .= trim($datos[$i]);
        }

        return $this->certificado;
    }

    public function xml() {
        $this->xml->formatOutput = true;
        return $this->xml->saveXML();
    }

    protected function parseName() {
        $serie = $this->comprobante->getAttribute('Serie') ? : 'F';
        $folio = $this->comprobante->getAttribute('Folio') ? : uniqid();
        return $serie . $folio . '.xml';
    }

    public function save($path, $name = null) {
        $this->setSello();
        $name = is_null($name) ? $this->parseName() : $name;
        
        $this->xml->formatOutput = true;
        if ($this->xml->save($xml = $path . $name)) {
            $this->validateXML($name);
            return $xml;
        }
        return false;
    }
// funcion que se encarga de validar el XML
    public function validateXML($name = null) {
        $validator = new DomValidator;
        $validated = $validator->validateFeeds($name);
        if ($validated) {
            echo "Feed successfully validated";
        } else {
            print_r($validator->displayErrors());
        }
    }

}
