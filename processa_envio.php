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

    // se cair na condicao de que a mensagem é invalida, nada será processado
    if(!$mensagem->mensagemValida()) {
        echo 'A mensagem não é valida';
        die();
    }

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'testedhweb@gmail.com';                 // SMTP username
        $mail->Password = '!teste@21';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('testedhweb@gmail.com', 'Web Completo Remetente');
        $mail->addAddress('chistina.pinto@gmail.com', 'Web Completo Destinatario');     // Add a recipient
        //$mail->addReplyTo('info@example.com', 'Information'); //<- caso respondam o email enviado, a resposta sera enviado automaticamente para este email
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments <- adicionar anexos ao email
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        // Conteúdo
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Oi, e-mail de teste do Dhyon';
        $mail->Body    = 'Oi, eu sou o conteúdo de teste do <strong>e-mail</strong> do Dhyon';
        // body alternativo para caso nao exista a marcação html no client destinatario
        $mail->AltBody = 'Oi, eu sou o conteúdo do e-mail';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo 'Não foi possivel enviar este email, por favor tente novamente mais tarde. ';
        echo 'Detalhes do erro: ' . $mail->ErrorInfo;
    }