<?php
include_once 'GenericController.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Enviarpropostaminc
 *
 * @author tisomar
 */
class EnviarpropostamincController extends GenericControllerNew {


    public function init() {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 121; // T�cnico
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }


    public function indexAction()
    {


    }


}
?>
