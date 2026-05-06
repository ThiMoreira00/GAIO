<?php
// template_email_acesso.php

$login = $dados['login'] ?? '';
$senha = $dados['senha'] ?? '';
$link = $dados['link'] ?? '';
$assunto = $dados['assunto'] ?? '';
$nome = $dados['nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($assunto ?? 'GAIO - Redefinição de senha') ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f9f9f9; font-family:Arial, sans-serif;">

<div style="padding:1.5rem; text-align:center; background-image: url('https://i.imgur.com/98LZPub.png');">
    <img src="https://i.imgur.com/XTfzUrf.png" alt="Logotipo GAIO" style="max-height:60px;">
</div>

<div style="max-width:600px; margin:20px auto; background-color:#fff; padding:30px; border-radius:6px; box-shadow:0 0 10px rgba(0,0,0,0.05);">
    <h2 style="color:#012a5c;">Olá, <?= htmlspecialchars($usuario_nome_reduzido) ?></h2>

    <p style="font-size:16px; color:#333;">
        Recebemos uma solicitação para redefinir a senha da sua conta no Sistema GAIO.
        <br><br>
        Caso tenha sido você, clique no botão abaixo para criar uma nova senha com segurança.
        <br>
        Se não foi você quem solicitou, recomendamos que troque sua senha imediatamente e entre em contato com o suporte.
    </p>

    <p style="text-align:center; margin:30px 0;">
        <a href="<?= htmlspecialchars($link_redefinicao) ?>" style="background-color:#0ea5e9; color:#fff; padding:12px 24px; text-decoration:none; border-radius:4px; font-weight:bold;">
            Redefinir Senha
        </a>
    </p>

    <p style="font-size:14px; color:#666;">
        Esse link é válido por 1 hora desde o envio desta mensagem e só pode ser usado uma vez.
        <br>
        Se o botão acima não funcionar, copie e cole o seguinte endereço no navegador:
        <br>
        <a href="<?= htmlspecialchars($link_redefinicao) ?>" style="color:#0056b3;"><?= htmlspecialchars($link_redefinicao) ?></a>
    </p>

    <p style="margin-top:20px; font-size:16px; color:#333;">
        Em caso de dúvidas, entre em contato com o suporte: <a href="mailto:<?= $_ENV['SISTEMA_SUPORTE_EMAIL'] ?>" style="color:#0056b3;"><?= $_ENV['SISTEMA_SUPORTE_EMAIL'] ?></a>
    </p>

    <p style="margin-top:20px;">Atenciosamente,<br>Sistema GAIO</p>
</div>

<div style="text-align:center; font-size:12px; color:#888; margin:20px 0;">
    GAIO <?= date('Y') ?> © Todos os direitos reservados.
    <br>
    Você recebeu este e-mail porque solicitou uma redefinição de senha. Se você não solicitou uma redefinição de senha, ignore este e-mail.
</div>

</body>
</html>
