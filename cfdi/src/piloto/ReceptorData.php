<?php namespace Piloto;

class ReceptorData extends CfdiData
{
    protected $Rfc;

    protected $Nombre;

    protected $ResidenciaFiscal; // nuevo campo

    protected $NumRegIdTrib; // Numero de registro de id del tribunal? verificar si es obligatorio

    protected $UsoCFDI; // Numero de registro de id del tribunal? verificar si es obligatorio

    public function __construct(array $data)
    {
        $this->parseData($data);
    }

    public function rules()
    {
        return [
            "Rfc" => 'required',
            "Nombre" => '', // max 254 caracteres
            "ResidenciaFiscal" => '',// clave del pais , de no ser nulo validar si corresponde al catalogo.
            "NumRegIdTrib" => '', // max 40
            "UsoCFDI" => 'required',
        ];
    }
}
