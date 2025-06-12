<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use SimpleXMLElement;

class AfipAuthController extends Controller
{
    private $wsdl;
    private $cert;
    private $privateKey;
    private $passphrase;
    private $url;
    private $taPath;

    public function __construct()
    {
        // Ruta al WSDL del WSAA (no WSFE)
        $this->wsdl      = storage_path('app/wsaa.wsdl'); // debe estar este archivo local
        // URL correcta para loginCms
        $this->url       = 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms'; 
        $this->taPath    = storage_path('app/xml/TA.xml');
        $this->cert      = storage_path('app/keys/certificate.crt');
        $this->privateKey = storage_path('app/keys/private.key');
        $this->passphrase = env('AFIP_KEY_PASS', '');
    }

    public function obtenerAutorizacion(Request $request)
    {
        $service = $request->input('service', 'wsfe');

        // Si ya tengo TA vigente, lo uso
        if (file_exists($this->taPath)) {
            $taXml = simplexml_load_file($this->taPath);
            $expirationTime = strtotime((string) $taXml->header->expirationTime);

            // Si TA válido por al menos 5 minutos
            if ($expirationTime > time() + 300) {
                return response()->json([
                    'mensaje' => 'Autorización reutilizada',
                    'TA'      => file_get_contents($this->taPath),
                ]);
            }
        }

        // Si no hay TA válido, genero uno nuevo
        $this->createTRA($service);
        $cms = $this->signTRA();
        $ta  = $this->callWSAA($cms);

        // Guardar el TA
        file_put_contents($this->taPath, $ta);

        return response()->json([
            'mensaje' => 'Autorización generada nueva',
            'TA'      => $ta,
        ]);
    }

    private function createTRA($service)
    {
        $TRA = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><loginTicketRequest version="1.0"></loginTicketRequest>');
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId', time());
        $TRA->header->addChild('generationTime', date('c', time() - 60));
        $TRA->header->addChild('expirationTime', date('c', time() + 3600)); // 1 hora válido
        $TRA->addChild('service', $service);

        // Guardar en storage (directo, no Storage facade para ruta absoluta)
        $TRA->asXML(storage_path('app/xml/TRA.xml'));
    }

    private function signTRA()
    {
        $traPath = storage_path('app/xml/TRA.xml');
        $tmpPath = storage_path('app/xml/TRA.tmp');

        if (!file_exists($this->privateKey)) {
            abort(500, "Error: Clave privada no encontrada.");
        }

        $status = openssl_pkcs7_sign(
            $traPath,
            $tmpPath,
            "file://" . $this->cert,
            ["file://" . $this->privateKey, $this->passphrase],
            [],
            PKCS7_DETACHED
        );

        if (!$status) {
            abort(500, "ERROR generando firma PKCS#7");
        }

        $signedData = file_get_contents($tmpPath);
        unlink($tmpPath);

        // Extraer CMS con regex seguro
        if (!preg_match('/-+BEGIN PKCS7-+\s*(.*)\s*-+END PKCS7-+/s', $signedData, $matches)) {
            abort(500, "No se pudo extraer CMS del archivo firmado.");
        }

        // Limpio saltos de línea y espacios
        $cms = trim(str_replace(["\r", "\n"], '', $matches[1]));

        return $cms;
    }

    private function callWSAA($cms)
    {
        try {
            $client = new SoapClient($this->wsdl, [
                'soap_version' => SOAP_1_2,
                'location' => $this->url,
                'trace' => 1,
                'exceptions' => 1, // mejor que 0 para capturar excepciones
            ]);

            $results = $client->loginCms(['in0' => $cms]);

            return $results->loginCmsReturn;
        } catch (\SoapFault $e) {
            abort(500, "SOAP Fault: " . $e->getMessage());
        }
    }
}
