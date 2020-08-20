<?php

namespace App\Http\Controllers;


use App\Email;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Exception;

class emailController extends Controller
{
    private $SucessLog, $FailLog;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function add(Request $request)
    {
        try {
            $emailsAdd = Email::filter($request->input('email'));
            $emailsRead = $this->lerArquivo();
            if($emailsRead!= "") {
                $emailsRead = Email::filter($this->lerArquivo());
                $emails = array_unique(array_merge($emailsAdd, $emailsRead));
            }
            else
                $emails = array_unique($emailsAdd);
            $this->escreverArquivo(Email::sort($emailsAdd), "emails_". time() .".txt");
            $this->escreverArquivo(Email::sort($emails), "emails.txt");
            return 'Email adicionado com sucesso';
        }
        catch (Exception $e)
        {
            return 'erro ' . $e;
        }
    }

    private function escreverArquivo($emails, $nome)
    {
        $handle = fopen($nome, 'w');
        if ($handle == false) die('Não foi possível criar o arquivo.');
        fwrite($handle, print_r(json_encode($emails), true));
        fclose($handle);
    }

    private function lerArquivo()
    {
        $emailsRead = "";
        $arquivo = "emails.txt";
        $handle = fopen($arquivo, 'r');
        if ($handle == false) die('Não foi possível criar o arquivo.');
        if(filesize($arquivo) > 0)
            $emailsRead = file_get_contents($arquivo);
        fclose($handle);
        return json_decode($emailsRead);
    }
    private function criarLogs()
    {
        $this->SuccessLog = new Logger('sent');
        $this->SuccessLog->pushHandler(new StreamHandler('sent.log', Logger::INFO));
        $this->FailLog = new Logger('fail');
        $this->FailLog->pushHandler(new StreamHandler('fail.log', Logger::INFO));
    }
    private function escreverLog($tipo, $texto)
    {
        if($tipo == "sent")
            $this->SuccessLog->info($texto);
        if($tipo == "fail")
            $this->FailLog->info($texto);
        return false;
    }

    public function send(Request $request)
    {
        TRY {
            $emails = Email::filter($this->lerArquivo());
            $countEmail = count($emails);
            $countSucess = 0;
            $countError = 0;
            $this->criarLogs();
            foreach ($emails as $email) {
                $e = new Email($email);
                $send = $e->send();
                if ($send) {
                    $texto = date("r") . " from: " . $email . ", to: " . $request->input("subject") . ", content: " . $request->input("body");
                    $this->escreverLog("sent", $texto);
                    $countSucess++;
                } else {
                    $texto = date("r") . " from: " . $email . ", to: " . $request->input("subject") . ", content: " . $request->input("body");
                    $this->escreverLog("fail", $texto);
                    $countError++;
                }
            }
            $retorno = (object)[
                "emails" => $countEmail,
                "emails_sent" => $countSucess,
                "emails_fail" => $countError,
                    ];
            return  json_encode($retorno);
        }
        catch (Exception $e)
        {
            return 'erro ' . $e;
        }
    }


    //
}
