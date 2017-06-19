<?php namespace Piloto;

class ImpuestosRetenidosData extends CfdiData
{
    protected $Impuesto;

    protected $Importe;

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Impuesto" => 'required',
            "Importe" => 'required',
        ];
    }
}
