<?php

namespace App\Http\Controllers;


use App\Email;
use Illuminate\Http\Request;
use Monolog\Logger;

class emailController extends Controller
{
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
            $emailsAdd = Email::filter($_POST['email']);
            var_dump($emailsAdd);
            die;
            $arquivo = fopen('emails.txt', 'r');
            if ($arquivo == false) die('Não foi possível criar o arquivo.');
            $emailsRead = Email::filter(file_get_contents($arquivo));
            fclose($arquivo);
            $emails = array_unique(array_merge($emailsAdd, $emailsRead));
            $arquivo = fopen('emails.txt', 'w');
            if ($arquivo == false) die('Não foi possível criar o arquivo.');
            fwrite($arquivo, print_r(Email::sort($emails), true));
            fclose($arquivo);
            return 'Email adicionado com sucesso';
        }
        catch (Exception $e)
        {
            return 'erro ' . $e;
        }
    }

    public function send(Request $request)
    {
        TRY {
            $arquivo = fopen('emails.txt', 'r');
            if ($arquivo == false) die('Não foi possível criar o arquivo.');
            $emails = Email::filter(file_get_contents($arquivo));
            fclose($arquivo);
            $SuccessLog = new Logger('sent');
            $SuccessLog->pushHandler(new StreamHandler('sent.log', Logger::INFO));
            $FailLog = new Logger('fail');
            $FailLog->pushHandler(new StreamHandler('fail.log', Logger::INFO));
            $countEmail = count($emails);
            $countSucess = 0;
            $countError = 0;
            foreach ($emails as $email) {
                $e = new Email($email);
                $send = $e->send();
                if ($send) {
                    $SuccessLog->info(date() . " " . $_POST['subject'] . " " . $_POST['body']);
                    $countSucess++;
                } else {
                    $FailLog->info(date() . " " . $_POST['subject'] . " " . $_POST['body']);
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
