<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Enumerations\EmailTipo;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $this->mail = new PHPMailer(true);

            // Configuração do servidor SMTP
            $this->mail->isSMTP();
            # $this->mail->SMTPDebug = 3;
            $this->mail->Host = $_ENV['MAIL_HOST'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV['MAIL_USUARIO'];
            $this->mail->Password = $_ENV['MAIL_SENHA'];
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
            # $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            # Apenas para localhost
            #$this->mail->SMTPSecure = false;
            # $this->mail->SMTPAutoTLS = false;

            $this->mail->Port = $_ENV['MAIL_PORTA'];

            $this->mail->setFrom($_ENV['MAIL_USUARIO'], $_ENV['MAIL_NOME']);

            $this->mail->isHTML(true);

        } catch (Exception $e) {

            var_dump($e->getMessage());
            error_log('Erro ao inicializar serviço de e-mail: ' . $e->getMessage());
        }
    }

    /**
     * Envia um e-mail.
     *
     * @param string $destinatario Email do destinatário
     * @param EmailTipo $tipo Tipo do e-mail (enum que define o template)
     * @param string $assunto Assunto do e-mail
     * @param array $dados Dados para popular o template
     * @return bool
     */
    public function enviar(string $destinatario, EmailTipo $tipo, string $assunto, array $dados = []): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($destinatario, $dados['nome'] ?? '');

            $corpo = $this->renderizarModelo($tipo, $dados);

            if(!$corpo) {
                throw new Exception('Erro ao renderizar modelo de e-mail.');
            }

            $this->mail->Subject = $assunto;
            $this->mail->Body = $corpo;

            return $this->mail->send();

        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Renderiza o template de e-mail com base no tipo fornecido.
     *
     * @param EmailTipo $tipo Tipo do e-mail (enum)
     * @param array $dados Dados para popular o template
     * @return string
     */
    private function renderizarModelo(EmailTipo $tipo, array $dados = []): string
    {
        try {
            $nomeArquivo = $tipo->value;
            $caminhoTemplate = __DIR__ . "/../../resources/emails/{$nomeArquivo}.php";

            if (!file_exists($caminhoTemplate)) {
                throw new Exception("Template de e-mail não encontrado: {$nomeArquivo}.php");
            }

            extract($dados);
            ob_start();
            include $caminhoTemplate;
            $corpo = ob_get_clean();
            return $corpo;

        } catch (Exception $e) {
            error_log('Erro ao renderizar modelo de e-mail: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Envia e-mail de bloqueio de conta.
     *
     * @param string $destinatario Email do destinatário
     * @param array $dados Dados para popular o template
     * @return bool
     */
    public function enviarEmailBloqueio(string $destinatario, array $dados = []): bool
    {
        return $this->enviar(
            $destinatario,
            EmailTipo::BLOQUEIO_CONTA,
            'GAIO - Bloqueio temporário da conta',
            $dados
        );
    }

    /**
     * Envia e-mail de redefinição de senha.
     *
     * @param string $destinatario Email do destinatário
     * @param array $dados Dados para popular o template
     * @return bool
     */
    public function enviarEmailRedefinicaoSenha(string $destinatario, array $dados = []): bool
    {
        return $this->enviar(
            $destinatario,
            EmailTipo::REDEFINICAO_SENHA,
            'GAIO - Redefinição de senha',
            $dados
        );
    }
}
