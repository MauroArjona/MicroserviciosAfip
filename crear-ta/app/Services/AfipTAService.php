<?php

namespace App\Services;

use Exception;
use SimpleXMLElement;

class AfipTAService
{
    public function generateTA(string $cuit, string $service = 'wsfe'): array
    {
        $basePath = storage_path("app/afip/$cuit");
        if (!is_dir($basePath)) {
            mkdir($basePath, 0700, true);
        }

        $certPath = "$basePath/cert_{$cuit}.crt";
        $keyPath  = "$basePath/key_{$cuit}.key";
        $taPath  = "$basePath/TA.xml";

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            throw new Exception("No se encontraron certificados para CUIT $cuit en $basePath");
        }

        // Crear archivo TRA
        $traXml = $this->createTRA($service);
        $traPath = "$basePath/TRA.xml";
        file_put_contents($traPath, $traXml);

        // Firmar TRA y obtener CMS
        $cms = $this->signTRA($traPath, $certPath, $keyPath);

        // Llamar al WSAA
        $soapResponse = $this->callWSAA($cms);

        // Guardar XML completo
        file_put_contents($taPath, $soapResponse);

        $xml = simplexml_load_string($soapResponse);
        if (!$xml) {
            throw new Exception("Error al parsear la respuesta XML de WSAA");
        }

        return [
            'token' => (string) $xml->credentials->token,
            'sign' => (string) $xml->credentials->sign,
            'vencimiento' => (string) $xml->header->expirationTime,
        ];
    }

    private function createTRA(string $service): string
    {
        $uniqueId = time();
        $generationTime = date('c', time() - 60);
        $expirationTime = date('c', time() + 3600);

        return <<<XML
<loginTicketRequest version="1.0">
  <header>
    <uniqueId>$uniqueId</uniqueId>
    <generationTime>$generationTime</generationTime>
    <expirationTime>$expirationTime</expirationTime>
  </header>
  <service>$service</service>
</loginTicketRequest>
XML;
    }

    private function signTRA(string $traPath, string $certPath, string $keyPath, string $passphrase = ''): string
    {
        $tmpOutput = tempnam(sys_get_temp_dir(), 'tra-out');

        $success = openssl_pkcs7_sign(
            $traPath,
            $tmpOutput,
            "file://$certPath",
            ["file://$keyPath", $passphrase],
            [],
            PKCS7_BINARY // importante para compatibilidad exacta
        );

        if (!$success) {
            unlink($tmpOutput);
            throw new \Exception("Error al firmar TRA con openssl_pkcs7_sign().");
        }

        $lines = file($tmpOutput);
        unlink($tmpOutput);

        // Salteamos las primeras 4 líneas del encabezado MIME
        $cms = '';
        for ($i = 4; $i < count($lines); $i++) {
            $cms .= trim($lines[$i]);
        }

        if (empty($cms)) {
            throw new \Exception("El contenido CMS está vacío luego de la limpieza.");
        }

        return $cms;
    }

    private function callWSAA(string $cms): string
    {
        $wsdl = storage_path('app/afip/wsaa.wsdl'); // opcional, si querés usar WSDL
        $wsaaUrl = env('AFIP_WSAA_URL', 'https://wsaa.afip.gov.ar/ws/services/LoginCms');

        $client = new \SoapClient($wsdl, [
            'location' => $wsaaUrl,
            'trace' => 1,
            'exceptions' => true,
            'soap_version' => SOAP_1_2,
        ]);

        try {
            $response = $client->loginCms(['in0' => $cms]);
        } catch (\SoapFault $e) {
            file_put_contents(storage_path('logs/last-request.xml'), $client->__getLastRequest());
            file_put_contents(storage_path('logs/last-response.xml'), $client->__getLastResponse());

            throw new Exception("Error en la llamada a WSAA: {$e->getMessage()} — Revisá storage/logs/last-response.xml para más info.");
        }

        return $response->loginCmsReturn;
    }
}
