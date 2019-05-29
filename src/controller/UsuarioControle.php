<?php

namespace Ouvidoria\controller;

use Ouvidoria\model\manager\AnexoManager;
use Ouvidoria\model\manager\HistoricoManager;
use Ouvidoria\model\manager\ManifestacaoManager;
use Ouvidoria\model\manager\OrgaoPublicoManager;
use Ouvidoria\model\manager\TipoManifestacaoManager;
use Ouvidoria\model\manager\TipoUsuarioManager;
use Ouvidoria\model\manager\UsuarioManager;
use Ouvidoria\model\Email;


class UsuarioControle extends AbstractControle
{
    public function __construct()
    {
        $this->email = new Email();
        $this->manifestacaoManager = new ManifestacaoManager();
        $this->anexoManager = new Anexomanager();
        $this->tipoManager = new TipoManifestacaoManager();
        $this->tipoUserManager = new TipoUsuarioManager();
        $this->usuarioManager = new UsuarioManager();
        $this->orgaoManager = new OrgaoPublicoManager();
        $this->historicoManager = new HistoricoManager();
        $this->inicializador();

    }

    public function inicializador()
    {
        $f = isset($_GET['function']) ? $_GET['function'] : "default";
        session_start();
        switch ($f) {
            case 'fazerLogin':
                $this->fazerLogin();
                break;
            case 'cadastrarUsuario':
                $this->cadastrarUsuario();
                break;
            case 'cadastrarUsuarioAcao':
                $this->cadastrarUsuarioAcao();
                break;
            case 'loginAcao':
                $this->loginAcao();
                break;
            case 'deslogar':
                unset($_SESSION['usuario']);
                session_destroy();
                $this->inicio();
                break;
            case 'alteraDadosAcao':
                $this->alteraDadosAcao();
                break;
            case 'alterarDados':
                $this->alterarDados();
                break;
            case 'detalharUsuario':
                $this->detalharUsuario();
                break;
            case 'usuarioDetalhe':
                $this->usuarioDetalhe();
                break;
            case 'inicial':
                $this->inicial();
                break;
            case 'listarUsuarios':
                $this->listarUsuarios();
                break;
            default:
                $this->inicio();
                break;
        }
        session_write_close();
    }

    public function inicial()
    {
        require('view/telaInicial.php');
    }

    public function alteraDadosAcao()
    {
        $usuario = $this->usuarioManager->buscaInfoUsuario($_SESSION['usuario']['cpf']);

        require('view/alterarDados.php');
    }

    public function alteraDadosAcao2(string $erro)
    {
        $usuario = $this->usuarioManager->buscaInfoUsuario($_SESSION['usuario']['cpf']);
        if ($erro == 1) {
            $emailUnico = false;
        } elseif ($erro == 2) {
            $senhaIgual = false;
        } elseif ($erro == 3) {
            $cpfExiste = false;
        } elseif ($erro == 4) {
            $senhaDiferenteBanco = false;
        }
        require('view/alterarDados.php');
    }

    public function inicio()
    {
        require('view/telaInicial.php');
    }

    public function cadastrarUsuarioAcao()
    {
        require('view/cadastrarUsuario.php');
    }

    public function loginAcao()
    {
        require('view/fazerLogin.php');
    }

    public function cadastrarUsuario()
    {
        if (isset($_POST['enviado'])) {

            $nomeCadastro = $_POST['nomeCadastro'];
            $cpfCadastro = $_POST['cpfCadastro'];
            $cpfValidado = $this->checaCPF($cpfCadastro);
            $enderecoCadastro = $_POST['enderecoCadastro'];
            $telefoneCadastro = $_POST['telefoneCadastro'];
            $emailCadastro = $_POST['emailCadastro'];
            $senha1 = $_POST['senhaCadastro'];
            $senha2 = $_POST['senhaConfirmacaoCadastro'];
            $senhaValidada = $this->comparaSenhas($senha1, $senha2);
            $tipo_usuario = 1;
            if ($cpfValidado) {//Verifica se o CPF existe
                if ($senhaValidada) {//Verifica se as senhas são iguais
                    $emailUnico = $this->checaEmailUnico($emailCadastro);
                    if ($emailUnico) {//Verifica se o email já foi cadastrado
                        if (strlen($senha1) >= 5) {
                            try {
                                $this->usuarioManager->registrarUsuario($cpfCadastro, $nomeCadastro, $enderecoCadastro, $telefoneCadastro, $emailCadastro, $senha1, $tipo_usuario);
                                $assunto = "Seja bem vindo";
                                $texto = "Seja bem vindo(a) " . $nomeCadastro . " ao nosso sistema de ouvidoria de Campo Grande.<br><br>
                                Sua conta foi criada com o e-mail " . $emailCadastro . ".<br><br>
                                Você pode fazer login através do nosso site ou clicando <a href='http://localhost/ouvidoria/src/index.php?section=UsuarioControle&function=loginAcao'>aqui</a>.";
                                $emailDestino = $emailCadastro;
                                $this->email->enviarEmail($emailDestino, $assunto, $texto);
                                echo "<script type=\"text/javascript\">alert(\"Usuário cadastrado com sucesso.\");</script>";
                                include 'view/fazerLogin.php';
                            } catch (Exception $e) {
                                $msg = $e->getMessage();
                            }
                        } else {
                            $erro = 5;
                            $this->errosTelaCadastro($nomeCadastro, $cpfCadastro, $enderecoCadastro, $telefoneCadastro, $emailCadastro, $senha1, $senha2, $erro);
                        }

                    } else {
                        $erro = 1;//Email existe
                        $this->errosTelaCadastro($nomeCadastro, $cpfCadastro, $enderecoCadastro, $telefoneCadastro, $emailCadastro, $senha1, $senha2, $erro);
                    }
                } else {
                    $erro = 2;//Senha não confere
                    $this->errosTelaCadastro($nomeCadastro, $cpfCadastro, $enderecoCadastro, $telefoneCadastro, $emailCadastro, $senha1, $senha2, $erro);
                }
            } else {
                $erro = 3;//CPF já existe
                $this->errosTelaCadastro($nomeCadastro, $cpfCadastro, $enderecoCadastro, $telefoneCadastro, $emailCadastro, $senha1, $senha2, $erro);
            }
        }
    }

    public function checaEmailUnico(string $emailUnico)
    {
        return $this->usuarioManager->checaEmail($emailUnico);
    }

    public function checaCPF(string $cpf)
    {
        return $this->usuarioManager->checaCPF($cpf);
    }

    public function errosTelaCadastro(string $nomeCadastro, string $cpfCadastro, string $enderecoCadastro, string $telefoneCadastro, string $emailCadastro, string $senha1, string $senha2, string $erro)
    {
        if ($erro == 1) {
            $emailUnico = false;
        } elseif ($erro == 2) {
            $senhaIgual = false;
        } elseif ($erro == 3) {
            $cpfExiste = false;
        } elseif ($erro == 4) {
            $senhaDiferenteBanco = false;
        } elseif ($erro == 5) {
            $senhaMenor = true;
        }
        $usuario = array();
        $usuario = (object)$usuario;
        $usuario->nome = $nomeCadastro;
        $usuario->cpf = $cpfCadastro;
        $usuario->endereco = $enderecoCadastro;
        $usuario->email = $emailCadastro;
        $usuario->telefone = $telefoneCadastro;
        $atualizar = true;
        include('view/cadastrarUsuario.php');
    }

    public function errosTelaAlterarDados(string $nomeAlterado, string $cpfAlterado, string $enderecoAlterado, string $telefoneAlterado, string $emailAlterado, string $senha1, string $senha2, string $erro)
    {
        if ($erro == 1) {
            $emailUnico = false;
        } elseif ($erro == 2) {
            $senhaIgual = false;
        } elseif ($erro == 3) {
            $cpfExiste = false;
        } elseif ($erro == 4) {
            $senhaDiferenteBanco = false;
        } elseif ($erro == 5) {
            $senhaMenor = true;
        }
        $usuario = array();
        $usuario = (object)$usuario;
        $usuario->nome = $nomeAlterado;
        $usuario->cpf = $cpfAlterado;
        $usuario->endereco = $enderecoAlterado;
        $usuario->email = $emailAlterado;
        $usuario->telefone = $telefoneAlterado;
        require('view/alterarDados.php');
    }

    public function alterarDados()
    {
        if (isset($_POST['enviado'])) {
            $senhaAntigaConfirmacao = ($_POST['senhaAntigaAlteraDados']);
            $cpfAlterado = $_POST['cpfAlteraDados'];
            $nomeAlterado = $_POST['nomeAlteraDados'];
            $enderecoAlterado = $_POST['enderecoAlteraDados'];
            $telefoneAlterado = $_POST['telefoneAlteraDados'];

            $emailAlterado = $_POST['emailAlteraDados'];
            $senha1 = $_POST['senhaNovaAlteraDados'];
            $senha2 = $_POST['senhaNovaConfirmacaoAlteraDados'];
            $id = $_SESSION['usuario']['id_tipo_usuario'];
            $emailBuscado = $this->usuarioManager->selecionarEmail($cpfAlterado);
            if ($emailAlterado == $emailBuscado['email']) {
                $emailUnico = true;
            } else {
                $emailUnico = $this->checaEmailUnico($emailAlterado);
            }
            if ($emailUnico) {
                if ($this->usuarioManager->verificaSenhaAntiga($senhaAntigaConfirmacao, $cpfAlterado)) {
                    if(empty($senha1) && empty($senha2)){
                        try {
                            if ($senha1 == "") {
                                $senha1 = $senhaAntigaConfirmacao;
                            }
                            $sucesso = $this->usuarioManager->alteraUsuario($cpfAlterado, $nomeAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $id);
                            echo "<script type=\"text/javascript\">alert(\"O usuário foi alterado com sucesso.\");</script>";
                            $this->alteraDadosAcao();
                        } catch (Exception $e) {
                            $msg = $e->getMessage();
                        }
                    }   else {
                        if (isset($senha1) && isset($senha2) && $this->comparaSenhas($senha1, $senha2)) {
                            if(strlen($senha1) >= 5 ){
                                try {
                                    $sucesso = $this->usuarioManager->alteraUsuario($cpfAlterado, $nomeAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $id);
                                    $this->alteraDadosAcao();
                                } catch (Exception $e) {
                                    $msg = $e->getMessage();
                                }
                            }else {
                                $erro = 5;
                                $this->errosTelaAlterarDados($nomeAlterado, $cpfAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $senha2, $erro);
                            }

                        }else{
                            $erro = 2;//Senha não confere
                            $this->errosTelaAlterarDados($nomeAlterado, $cpfAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $senha2, $erro);
                        }}
                }else {
                    $erro = 4;//Senha não confere com a salva no banco de dados
                    $this->errosTelaAlterarDados($nomeAlterado, $cpfAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $senha2, $erro);
                }

                //header('Location: view/alterarDados.php');
            } else {
                $erro = 1;//Email existe
                $this->errosTelaAlterarDados($nomeAlterado, $cpfAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado, $senha1, $senha2, $erro);
            }
        }
    }

    public function comparaSenhas($senha1, $senha2)
    {
        if ($senha1 == $senha2)
            return true;
        else
            return false;
    }

    public function fazerLogin()
    {
        if (isset($_GET["id"])) {
            $cpf = isset($_POST["cpf"]) ? addslashes(trim($_POST["cpf"])) : FALSE;
            $senha = isset($_POST["senha"]) ? $_POST["senha"] : FALSE;

            if (!$cpf || !$senha) {
                echo 'Você não tem permissão para acessar essa página.';
            } else {

                $usuario = $this->usuarioManager->validaUsuario($cpf, $senha);

                $_SESSION['usuario'] = $usuario;

                if ($_SESSION['usuario']['id_tipo_usuario'] == 1 || $_SESSION['usuario']['id_tipo_usuario'] == 2 || $_SESSION['usuario']['id_tipo_usuario'] == 3 || $_SESSION['usuario']['id_tipo_usuario'] == 4) {

                    $checaInteresse = 0;
                    $idUsuario = $_SESSION['usuario']['cpf'];
                    $manifestacao = $this->manifestacaoManager->selecionaManifestacaoCidadao($_GET['id']);
                    $idManifestacao = $manifestacao->id_manifestacao;
                    if ($this->manifestacaoManager->checaInteresse($idManifestacao, $idUsuario)) {
                        $checaInteresse = 1;
                    }
                    require('view/detalheManifestacaoCidadao.php');
                    session_write_close();
                } else {
                    $id = $_GET['id'];
                    $msgLogin = false;
                    require('view/fazerLogin.php');
                }
            }
        } else {
            $cpf = isset($_POST["cpf"]) ? addslashes(trim($_POST["cpf"])) : FALSE;
            $senha = isset($_POST["senha"]) ? $_POST["senha"] : FALSE;

            if (!$cpf || !$senha) {
                //tratar erros
                echo 'Você não tem permissão para acessar essa página.';
            } else {

                $usuario = $this->usuarioManager->validaUsuario($cpf, $senha);

                $_SESSION['usuario'] = $usuario;

                // Usuario Cidadao
                if ($_SESSION['usuario']['id_tipo_usuario'] == 1) {
                    $listaTipos = $this->tipoManager->listaTipos();
                    require('view/criarManifestacao.php');

                } // Usuario Ouvidor
                else if ($_SESSION['usuario']['id_tipo_usuario'] == 2) {
                    $this->listar();
                } // Usuario Administrador Publico
                else if ($_SESSION['usuario']['id_tipo_usuario'] == 3)
                    $this->listar();

                //Usuario Administrador Sistema
                else if ($_SESSION['usuario']['id_tipo_usuario'] == 4) {
                    $this->listarUsuarios();
                } else if ($_SESSION['usuario']['id_tipo_usuario'] == 5) {
                    echo "<script type=\"text/javascript\">alert('Usuário desativado! Por favor entre em contato com o suporte e solicite acesso.');</script>";
                    $this->loginAcao();
                } else {
                    $msgLogin = false;
                    require('view/fazerLogin.php');
                }
            }
        }
    }

    public function listar()
    {
        $nvlAcesso = isset($_SESSION['usuario']['id_tipo_usuario']) ? $_SESSION['usuario']['id_tipo_usuario'] : null;

        if (!is_null($nvlAcesso)) {
            $dados = $this->manifestacaoManager->listaManifestacoes($nvlAcesso);
            require('view/listarManifestacoes.php');
        } else {
            echo 'Você não tem permissão para acessar essa página.';
            exit();
        }
    }

    public function listarUsuarios()
    {
        $nvlAcesso = isset($_SESSION['usuario']['id_tipo_usuario']) ? $_SESSION['usuario']['id_tipo_usuario'] : null;

        if (!is_null($nvlAcesso)) {
            $dados = $this->usuarioManager->listaUsuarios();
            require('view/listarUsuarios.php');
        } else {
            echo 'Você não tem permissão para acessar essa página.';
            exit();
        }
    }

    public function detalharUsuario()
    {
        $tipos = $this->tipoUserManager->listaTipos();
        $usuario = $this->usuarioManager->buscaInfoUsuarioDetalhe($_GET['cpf']);
        require('view/detalheUsuario.php');
    }

    public function detalharUsuarioComErro()
    {
        $erro = false;
        $tipos = $this->tipoUserManager->listaTipos();
        $usuario = $this->usuarioManager->buscaInfoUsuarioDetalhe($_GET['cpf']);
        require('view/detalheUsuario.php');
    }

    public function usuarioDetalhe()
    {
        if (isset($_POST['acao'])) {
           if ($_POST['acao'] == "Alterar") {
                $nomeAlterado = $_POST['nomeAlteraDados'];
                $enderecoAlterado = $_POST['enderecoAlteraDados'];
                $telefoneAlterado = $_POST['telefone'];
                $emailAlterado = $_POST['emailAlteraDados'];
                $cpfAlterado = $_POST['cpfAlteraDados'];

               $emailBuscado = $this->usuarioManager->selecionarEmail($cpfAlterado);
               if ($emailAlterado == $emailBuscado['email']) {
                   $emailUnico = true;
               } else {
                   $emailUnico = $this->checaEmailUnico($emailAlterado);
               }
               if ($emailUnico) {
                   try {
                       $this->usuarioManager->alteraDados($cpfAlterado, $nomeAlterado, $enderecoAlterado, $telefoneAlterado, $emailAlterado);
                       echo "<script type=\"text/javascript\">alert('O usuário foi alterado com sucesso!');</script>";
                       $this->listarUsuarios();
                   } catch (Exception $e) {
                       $msg = $e->getMessage();
                   }

               }
                else {
                    $_GET['cpf'] = $cpfAlterado;
                    $this->detalharUsuarioComErro();
               }


            }
        } else {
            if ($_GET['privilegio'] == null) {
                echo "<script type=\"text/javascript\">alert('O privilégio deve ser informado!');</script>";
                $this->detalharUsuario();
            } else {
                $this->usuarioManager->delegarPrivilegios($_GET['cpf'], $_GET['privilegio']);
                $this->listarUsuarios();
            }
        }

    }

}