<?php

    require "./libs/PHPMailer/Exception.php";
    require "./libs/PHPMailer/OAuth.php";
    require "./libs/PHPMailer/PHPMailer.php";
    require "./libs/PHPMailer/POP3.php"; // CONTEM AS ESPECIFICACOES DO PROTOCOLO DE RECEBIMENTO DE EMAIL
    require "./libs/PHPMailer/SMTP.php"; // CONTEM O PROTOCOLO DE ENVIO DE EMAIL

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception; 

    //print_r($_POST);

    class Mensagem {
        private $para = null;
        private $assunto = null;
        private $mensagem = null;
        public $status = array('codigo_status' => null, 'descricao_status' => '');

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        public function mensagemValida() {
            // verificar se algum atributo está vazio antes de enviar o email
            // empty() retorna true se o objeto esta vazio
            if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
                return false;
            }
            return true;
        }
    }

    $mensagem = new Mensagem();

    // atribuindo os nomes dos atributos e os valores dos atributos atraves da superglobalpost pelos indices criados no method post dos names do form 
    $mensagem->__set('para', $_POST['para']);
    $mensagem->__set('assunto', $_POST['assunto']);
    $mensagem->__set('mensagem', $_POST['mensagem']);

    // se cair na condicao de que a mensagem é invalida, nada será processado e será forçado para a pagina index.php
    if(!$mensagem->mensagemValida()) {
        echo 'A mensagem não é valida';
        // metodo que força o processo para algum lugar
        header('Location: index.php');
    }

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = false;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'testedhweb@gmail.com';                 // SMTP username
        $mail->Password = '!teste@21';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('testedhweb@gmail.com', 'Web Completo Remetente');
        $mail->addAddress($mensagem->__get('para'));     // Add a recipient
        //$mail->addReplyTo('info@example.com', 'Information'); //<- caso respondam o email enviado, a resposta sera enviado automaticamente para este email
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments <- adicionar anexos ao email
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        // Conteúdo
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $mensagem->__get('assunto');
        $mail->Body    = $mensagem->__get('mensagem');
        // body alternativo para caso nao exista a marcação html no client destinatario
        $mail->AltBody = 'É necessário utilizar um client que suporte html para ter acesso total ao conteúdo dessa mensagem';

        $mail->send();

        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
    } catch (Exception $e) {
        $mensagem->status['codigo_status'] = 2;
        $mensagem->status['descricao_status'] = 'Não foi possivel enviar este email, por favor tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;
    }
?>

<html>

<head>
    <meta charset="utf-8" />
    <title>App Mail Send</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>

        <div class="row">
            <div class="col-md-12">
                <? if($mensagem->status['codigo_status'] == 1) { ?>

                    <div class="container">
                        <h1 class="display-4 text-success">Sucesso</h1>
                        <p><? $mensagem->status['descricao_status']; ?></p>
                        <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                    </div>
                    
                <? } ?>
                <? if($mensagem->status['codigo_status'] == 2) { ?>

                    <div class="container">
                        <h1 class="display-4 text-danger">Ops! Tivemos um erro...</h1>
                        <p> <? $mensagem->status['descricao_status']; ?> </p>
                        <a href="index.php" class="btn btn-danger btn-lg mt-5 text-white">Voltar</a>
                    </div>
                    
                <? } ?>
            </div>
        </div>
    </div>
</body>
</html>