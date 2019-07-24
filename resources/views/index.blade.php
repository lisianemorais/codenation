<!doctype html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/app.css">
        <link rel="stylesheet" href="/css/custom.css">
        <title> CodeNation </title>
    </head>
    <body>
    <div class="content">
        <h1>Desafio</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <table>
                    <tr>
                        <th>Mensagem Cifrada</th>
                        <th>Mensagem Decifrada</th>
                        <th>Resumo Criptografado</th>
                    </tr>
                    <tr>
                        <td>{{$desafio->cifrado}}</td>
                        <td>{{$desafio->decifrado}}</td>
                        <td>{{$desafio->resumo_criptografico}}</td>
                    </tr>

                </table>

            </div>
        </div>
    </div>
    </body>
</html>
