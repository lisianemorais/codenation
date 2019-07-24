<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CriptografiaController extends Controller
{
    private $alfabeto = [];

    public function __construct()
    {
        $this->alfabeto = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
    }

    public function index()
    {
        $client = new Client([
            'base_uri' => 'https://api.codenation.dev/v1/challenge/dev-ps/generate-data?token=ee86ea5dfd1784bc4672d9adfb11fd00d55bb688',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', '');

        $desafioCriptografado = json_decode($response->getBody()->getContents());

        $this->geraArquivoJson($desafioCriptografado);
        $this->decifraMensagem();
        $this->geraResumoCriptografado();
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
        $this->geraArquivoJson($desafioCriptografado);
    }

    public function getCaracterDescriptografado($indiceCifrado, $numeroCasas)
    {
        $indiceDecifrado = $indiceCifrado - $numeroCasas;
        if ($indiceDecifrado < 0) {
            $indiceDecifrado = sizeof($this->alfabeto) - $indiceCifrado;
        }
        return  $this->alfabeto[$indiceDecifrado];
    }

    public function geraResumoCriptografado()
    {
        $desafioCriptografado = $this->getAnswerJson();
        $desafioCriptografado->resumo_criptografico = sha1($desafioCriptografado->decifrado);
        $this->geraArquivoJson($desafioCriptografado);
    }

    public function geraArquivoJson($dados_json)
    {
        $dados_json = json_encode($dados_json);
        file_put_contents('answer.json', $dados_json);
    }

    public function getAnswerJson()
    {
        // Atribui o conteúdo do arquivo para variável $arquivo
        $arquivo = file_get_contents('answer.json');
        // Decodifica o formato JSON e retorna um Objeto
        $json = json_decode($arquivo);

        return $json;
    }


}
