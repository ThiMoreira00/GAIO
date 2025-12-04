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
    <title><?= htmlspecialchars($assunto ?? 'Seus dados de acesso') ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f9f9f9; font-family:Arial, sans-serif;">

<div style="padding:1.5rem; text-align:center; background-image: url('https://i.imgur.com/98LZPub.png');">
    <img src="https://i.imgur.com/XTfzUrf.png" alt="Logotipo GAIO" style="max-height:60px;">
</div>

<div style="max-width:600px; margin:20px auto; background-color:#fff; padding:30px; border-radius:6px; box-shadow:0 0 10px rgba(0,0,0,0.05);">
    <h2 style="color:#012a5c;">Olá, <?= htmlspecialchars($usuario_nome_reduzido) ?></h2>

    <p style="font-size:16px; color:#333;">Sua conta foi temporariamente bloqueada após muitas tentativas de login com senha incorreta.
        <br>
        Aguarde alguns minutos e tente novamente.
        <br>
        Se não foi você quem tentou acessar, recomendamos que troque sua senha assim que possível.
        <br>
        Em caso de dúvidas, entre em contato com o suporte: <a href="mailto:<?= $_ENV['SISTEMA_SUPORTE_EMAIL'] ?>" style="color:#0056b3;"><?= $_ENV['SISTEMA_SUPORTE_EMAIL'] ?></a>
    </p>

    <p style="margin-top:20px;">Atenciosamente,<br>Sistema GAIO</p>
</div>

<div style="text-align:center; font-size:12px; color:#888; margin:20px 0;">
    GAIO <?= date('Y') ?> © Todos os direitos reservados.
</div>

</body>
</html>
