<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CriptografiaController extends Controller
{
    protected $alfabeto = [];
    protected $client ;

    public function __construct()
    {
        $this->alfabeto = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
        $this->client = $client = new Client([
            'base_uri' => 'https://api.codenation.dev/v1/challenge/dev-ps/',
        ]);
    }

    public function index()
    {
        $response = $this->client->request('GET', 'generate-data?token=1352b05ea2f10f9841baa65009e5ca5ccd0a5285');
        $desafioCriptografado = json_decode($response->getBody()->getContents());

        $this->geraArquivoJson($desafioCriptografado);
        $this->decifraMensagem();
        $this->geraResumoCriptografado();
        $this->submeteCriptografia();
        $desafio  = $this->getAnswerJson();
        return view('index', compact('desafio') );

    }

    public function decifraMensagem()
    {
        $mensagemDecifrada = '';
        $desafioCriptografado = $this->getAnswerJson();
        if (!empty($desafioCriptografado->cifrado)) {
            $cifra = str_split($desafioCriptografado->cifrado);
            $items = 1;
            foreach ($cifra as $item) {

                switch ($item){
                    case is_int($item):
                    case ':':
                    case '.':
                    case ' ':
                        $mensagemDecifrada .= $item;
                        break;
                    default:
                        $indiceCifrado = array_search($item, $this->alfabeto);
                        $mensagemDecifrada .= $this->getCaracterDescriptografado($indiceCifrado, $desafioCriptografado->numero_casas);
                        break;
                }
            }
        }
        $desafioCriptografado->decifrado = $mensagemDecifrada;
        var_dump($mensagemDecifrada);
        
        $this->geraArquivoJson($desafioCriptografado);
    }

    public function getCaracterDescriptografado($indiceCifrado, $numeroCasas)
    {
       // {"numero_casas":11,"token":"1352b05ea2f10f9841baa65009e5ca5ccd0a5285","cifrado":"ty escpp hzcod t nly dfx fa pgpcjestyr t slgp wplcypo lmzfe wtqp: te rzpd zy. czmpce qczde","decifrado":"in three words i can sum up everything i have learned about life: it goes on. robert frost","resumo_criptografico":"b946be28903c3a1ed04aa27b52c366922033277b"}
        $indiceDecifrado = $indiceCifrado - $numeroCasas;

        if ($indiceDecifrado < 0) {
            $indiceDecifrado = sizeof($this->alfabeto) - abs($indiceDecifrado);
        }
       
        return  $this->alfabeto[$indiceDecifrado];
    }

    public function geraResumoCriptografado()
    {
        $desafioCriptografado = $this->getAnswerJson();
        $desafioCriptografado->resumo_criptografico = sha1($desafioCriptografado->decifrado);
        $this->geraArquivoJson($desafioCriptografado);
    }

    public function geraArquivoJson($dadosJson)
    {
        $dadosJson = json_encode($dadosJson);
        file_put_contents('answer.json', $dadosJson);
    }

    public function getAnswerJson()
    {
        // Atribui o conteúdo do arquivo para variável $arquivo
        $arquivo = file_get_contents('answer.json');
        // Decodifica o formato JSON e retorna um Objeto
        $dadosArquivo = json_decode($arquivo);

        return $dadosArquivo;
    }

    public function submeteCriptografia()
    {
        $response = $this->client->request('POST', 'submit-solution?token=1352b05ea2f10f9841baa65009e5ca5ccd0a5285', [
            'multipart' => [
                [
                    'name'     => 'answer',
                    'contents' => fopen('answer.json', 'r')
                ],
            ],
        ]);
    }
}
