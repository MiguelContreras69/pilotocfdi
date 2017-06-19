<?php namespace Piloto;
// SE MODIFICO EL SCRIPT PARA QUE SE AJUSTE AL LOS CAMBIOS DE ETIQUETAS Y CAMPOS
// QUE SE AÑADIERON EN LA VERSION 3.3
class GeneralData extends CfdiData
{
    // el orden en el que estan declaradas las variables se muestran en el XML
    // las variables se heredan a las subclases
    protected $Version = '3.3';

    protected $Serie;

    protected $Folio;

    protected $Fecha;

    protected $Sello;
 
    protected $NoCertificado;

    protected $Certificado;

    protected $SubTotal;

    protected $Moneda;

    protected $Total;

    protected $TipoDeComprobante;

    protected $FormaPago;

    protected $MetodoPago;
    
    protected $CondicionesDePago;

    protected $Descuento;

    protected $TipoCambio;

    protected $MotivoDescuento;

    protected $LugarExpedicion;

    protected $FolioFiscalOrig;

    protected $SerieFolioFiscalOrig;

    protected $FechaFolioFiscalOrig;

    protected $MontoFolioFiscalOrig;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }
// reglas de validacion para declarar que atributos seran obligatorios
    
  /*
   * se eliminaran ciertos campos dependiendo de los atributos de las condiciones de pago
   * 
   */  
    
    
    
    
    public function rules()
    {
        return [
            "Serie" => 'required',
            "Folio" => '',
            "Fecha" => 'required',
            "Sello" => '',// verificar la encriptacion en sha 256 
            "FormaPago" => 'required',
            "NoCertificado" => 'required', // max 20
            "Certificado" => '',
            "CondicionesDePago" => '', // max 1000 min 1
            "SubTotal" => 'required', // crear metodo que realize el calculo de este campo
            "Descuento" => '',
            "MotivoDescuento" => '', // este campo ?
            "TipoCambio" => 'required', // puede ser opcional pero para evitar futuros conflictos se pondra obligatorio, si es MXN sera 1 de caso contrario el usuario escribira el valor de la divisa equivalente
            "Moneda" => 'required',
            "Total" => 'required',// verificar calculo
            "TipoDeComprobante" => 'required',
            "FormaPago" => 'required',
            "MetodoPago" => 'required',
            "LugarExpedicion" => 'required',// codigo postal,
            //"NumCtaPago" => 'required', // este campo no se utliliza
            "Confirmacion" => '',
            "FolioFiscalOrig" => '',
            "SerieFolioFiscalOrig" => '',
            "FechaFolioFiscalOrig" => '',
            "MontoFolioFiscalOrig" => '',
        ];
    }
}
