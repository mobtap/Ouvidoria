<?php
session_start();
require ('model/Conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Detalhe Manifesta��o</title>
    <meta charset="utf-8">
    <meta http-equiv=�content-type� content="text/html;" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="tags, que, eu, quiser, usar, para, os, robos, do, google" />
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="shortcut icon" href="logo.jpg"/>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- ESTILOS PARA ESTA P�GINA -->
    <!-- Nesse caso, este estilo � apenas para inserir imagens -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />

    <!-- JAVASCRIPT E JQUERY -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</head>
<body>
<?php
/*
var_dump($manifestacao);
if(!isset($_SESSION['CPF'])){
    header('Location: ../index.php');
}
*/
?>

<!--MENU SUPERIOR -->

<?php include('menu.php'); ?>

<!--FIM MENU SUPERIOR -->

<!-- TELA DE CADASTRO -->
<div class="container mt-3">
    <form id="form" class="row" action="?section=ManifestacaoControle&function=responder" method="post">
        <div class="form-group col-6">
            <label for="protocolo">Protocolo:</label>
            <input value="<?=$manifestacao->id_manifestacao?>" name="protocolo" id="protocolo" readonly class="form-control">
        </div>
        <div class="form-group col-6">
            <label for="data">Data de Cria��o:</label>
            <input id="data" class="form-control" value="<?=date('d-m-Y', strtotime($manifestacao->data_manifestacao))?>" readonly>
        </div>
        <div class="form-group col-12">
            <label for="assunto">Assunto:</label>
            <input id="assunto" class="form-control" value="<?=$manifestacao->assunto?>" readonly>
        </div>
        <div class="form-group col-12">
            <label for="prop">Propriet�rio:</label>
            <input id="prop" class="form-control" value="<?=$manifestacao->nome?>" readonly>
        </div>
        <div class="form-group col-6">
            <label for="situacao">Situa��o:</label>
            <input id="situacao" class="form-control" value="<?=$manifestacao->nome_situacao?>" readonly>
        </div>
        <div class="form-group col-6">
            <label for="setor">Setor Respons�vel:</label>
            <input id="setor" value="<?php if(isset($manifestacao->nome_orgao_publico)) echo '$manifestacao->nome_orgao_publico'; else
			echo 'Esta manifesta��o ainda n�o foi encaminhada'?>" readonly class="form-control"/>
        </div>
        <div class="form-group col-12">
            <label for="descricao">Descri��o:</label>
            <textarea name="mensagem" rows="6" class="form-control" maxlength="1000" readonly><?=$manifestacao->mensagem?></textarea>
        </div>
        <div class="form-group col-12">
            <label for="resposta">Resposta:</label>
            <textarea name="mensagem" rows="6" class="form-control" maxlength="1000" readonly><?php if (isset($manifestacao->resposta)) echo $manifestacao->resposta; else echo "";?></textarea>
        </div>
        <div class="form-group col-12"> 
			<a class="btn btn-success float-right" href="?section=ManifestacaoControle&function=acompanharManifestacaoAcao"><i class="fa fa-check">Voltar</a>
        </div>
    </form>
</div>

</body>
