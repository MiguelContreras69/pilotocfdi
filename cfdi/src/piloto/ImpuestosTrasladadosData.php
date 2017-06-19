<?php namespace Piloto;

class ImpuestosTrasladadosData extends CfdiData
{
    protected $Impuesto;

    protected $Tasa;

    protected $Importe;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Impuesto" => 'required',
            "TipoFactor" => 'required',
            "TasaOCuota" => 'required',
            "Importe" => 'required',
        ];
    }
}
