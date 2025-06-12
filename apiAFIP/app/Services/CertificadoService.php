<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CertificadoService
{
    private $privateKeyPath = 'storage/keys/30717457613.key';
    private $certPath = 'storage/keys/certificado.pem';
    private $csrPath = 'storage/keys/certificado.csr';

    public function generarNuevoCertificado()
    {
        if (!file_exists(storage_path($this->privateKeyPath))) {
            abort(500, "Error: Clave privada no encontrada.");
        }

        // Configuración del certificado
        $dn = [
            "countryName" => "AR",
            "stateOrProvinceName" => "Buenos Aires",
            "localityName" => "Comodoro Rivadavia",
            "organizationName" => "",
            "commonName" => "",
            "emailAddress" => ""
        ];

        // Generar solicitud de certificado (CSR)
        $privKey = openssl_pkey_get_private("file://" . storage_path($this->privateKeyPath), "XXXXX"); // Reemplaza con la passphrase correcta
        $csr = openssl_csr_new($dn, $privKey, ["digest_alg" => "sha256"]);

        if (!$csr) {
            abort(500, "Error generando la solicitud de certificado (CSR).");
        }

        // Crear el nuevo certificado válido por 1 año
        $cert = openssl_csr_sign($csr, null, $privKey, 365, ["digest_alg" => "sha256"]);

        if (!$cert) {
            abort(500, "Error al firmar el nuevo certificado.");
        }

        // Guardar el certificado en storage
        openssl_x509_export($cert, $certOut);
        Storage::disk('local')->put('keys/certificado.pem', $certOut);
        openssl_csr_export($csr, $csrOut);
        Storage::disk('local')->put('keys/certificado.csr', $csrOut);

        return response()->json(["mensaje" => "Nuevo certificado generado"]);
    }
}
