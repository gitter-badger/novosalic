<?php
/**
 * DddController
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class DddController extends Zend_Controller_Action
{
	/**
	 * M�todo para buscar os ddds de um estado
	 * @param void
	 * @return void
	 */
	public function dddAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

		// recebe o id via post
		$post = Zend_Registry::get('post');
		$id = (int) $post->id;

		// integra��o MODELO e VIS�O
		$this->view->ddds = Ddd::buscar($id);
	}



	/**
	 * M�todo para buscar os ddds de um estado
	 * Busca como XML para o AJAX
	 * @access public
	 * @param void
	 * @return void
	 */
	public function comboAction()
	{
		$this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

		// recebe o id via post
		$post = Zend_Registry::get('post');
		$id = (int) $post->id;

		// integra��o MODELO e VIS�O
		$this->view->comboddds = Ddd::buscar($id);
	} // fecha comboAction()


	/**
	 * M�todo para buscar os ddds de um estado
	 * Busca como XML para o AJAX
	 * @access public
	 * @param void
	 * @return void
	 */
	public function comboSalvarTelefoneAction()
	{
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

            // recebe o id via post
            $post = Zend_Registry::get('post');
            $id = (int) $post->id;

            // integra��o MODELO e VIS�O
            $arrayDados = Ddd::buscar($id);
            $dados = array();
            $i = 0;
            if(count($arrayDados) > 0){
                foreach ($arrayDados as $value) {
                    $dados[$i]['id'] = $value->id;
                    $dados[$i]['descricao'] = $value->descricao;
                    $i++;
                }
                $jsonEncode = json_encode($dados);
                echo json_encode(array('resposta'=>true,'conteudo'=>$dados));
            } else {
                echo json_encode(array('resposta'=>false));
            }
            die();
	} // fecha comboAction()
}