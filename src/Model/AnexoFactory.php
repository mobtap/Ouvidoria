<?php

include("model/Connection.php");
require_once("Anexo.php");
require_once("AbstractFactory.php");
class AnexoFactory extends AbstractFactory
{
    public function __construct() {

        parent::__construct();
    }

    public function salvar($obj) {
        global $connection;
        $anexo = $obj;

        try {
            $query = "INSERT INTO anexo (id_anexo, caminho,nome_anexo) VALUES ('"
                . $anexo->getIdAnexo() . "', '"
                . $anexo->getCaminho()."','"
                . $anexo->getNomeAnexo()."')";

            if (mysqli_query($connection,$query)) {
                $result = true;
            } else {
                $result = false;
            }
        } catch (PDOException $exc) {
            echo $exc->getMessage();
            $result = false;
        }
        return $result;
    }

}