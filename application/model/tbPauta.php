<?php
class tbPauta extends GenericModel {

    protected $_banco = "BDCORPORATIVO";
    protected $_schema = "scSAC";
    protected $_name = "tbPauta";

    public function buscarProjetosAvaliados($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $bln_readequacao=false){
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slctVlAprovado = $this->select();
        $slctVlAprovado->setIntegrityCheck(false);
        $slctVlAprovado->from(
                        array("tbPlanilhaAprovacao"),
                        array(new Zend_Db_Expr("ROUND(SUM(qtItem*nrOcorrencia*vlUnitario),2)")),
                        "SAC.dbo"
                     );
        $slctVlAprovado->where("IdPRONAC = p.idPronac and stAtivo='S' and nrFonteRecurso=109");


        $slct->from(
                        array('t'=>$this->_schema.'.'.$this->_name),
                        array('idNrReuniao', 'IdPronac', 'VlAprovado'=>"({$slctVlAprovado})", "stAnalise")
                     );
        $slct->joinInner(
                            array('z'=>'tbDistribuicaoProjetoComissao'),
                            't.IdPRONAC = z.idPRONAC',
                            array("idComponente"=>"idAgente"),
                            'BDCORPORATIVO.scSAC'
                          );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                            't.idPronac = p.idPronac',
                            array("Pronac"=>new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"), "NomeProjeto", "Situacao", "Area", "Segmento"),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('d'=>'tbDistribuirParecer'),
                            'p.IdPRONAC = d.idPRONAC',
                            array("idOrgao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('s'=>'Situacao'),
                            'p.Situacao = s.Codigo',
                            array("DescSituacao"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array("DescArea"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array("DescSegmento"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('n1'=>'Nomes'),
                            'z.idAgente = n1.idAgente',
                            array("Componente"=>"Descricao"),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('x'=>'Agentes'),
                            'p.CgcCpf = x.CNPJCPF',
                            array(),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('n'=>'Nomes'),
                            'x.idAgente = n.idAgente',
                            array("Proponente"=>"Descricao"),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('r'=>'tbReuniao'),
                            't.idNrReuniao = r.idNrReuniao',
                            array(),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('o'=>'Orgaos'),
                            'd.idOrgao = o.Codigo',
                            array("Orgao"=>"Sigla"),
                            "SAC.dbo"
                          );

        //=============== VALOR SOLICITADO ======================//
        if(!$bln_readequacao){
            $slctVlSolicitado = $this->select();
            $slctVlSolicitado->setIntegrityCheck(false);
            $slctVlSolicitado->from(
                            array("tbPlanilhaProposta"),
                            array(new Zend_Db_Expr("ROUND(SUM(Quantidade*Ocorrencia*ValorUnitario),2)")),
                            "SAC.dbo"
                            );
            $slctVlSolicitado->where("idProjeto = p.idProjeto and idProduto <> '206'");
        }else{
            $slctVlSolicitado = $this->select();
            $slctVlSolicitado->setIntegrityCheck(false);
            $slctVlSolicitado->from(
                            array("tbPlanilhaAprovacao"),
                            array(new Zend_Db_Expr("ROUND(SUM(qtItem*nrOcorrencia*vlUnitario),2)")),
                            "SAC.dbo"
                            );
            $slctVlSolicitado->where("idPronac = p.idPronac AND idProduto <> '206' AND tpPlanilha = 'SR'");
        }


        //=============== VALOR SUGERIDO ======================//
        if(!$bln_readequacao){
            $slctVlSugerido = $this->select();
            $slctVlSugerido->setIntegrityCheck(false);
            $slctVlSugerido->from(
                            array("tbPlanilhaProjeto"),
                            array(new Zend_Db_Expr("ROUND(SUM(Quantidade*Ocorrencia*ValorUnitario),2)")),
                            "SAC.dbo"
                         );
            $slctVlSugerido->where("IdPRONAC = p.idPronac and idProduto <> '206'");
        }else{
            $slctVlSugerido = $this->select();
            $slctVlSugerido->setIntegrityCheck(false);
            $slctVlSugerido->from(
                            array("tbPlanilhaAprovacao"),
                            array(new Zend_Db_Expr("ROUND(SUM(qtItem*nrOcorrencia*vlUnitario),2)")),
                            "SAC.dbo"
                            );
            $slctVlSugerido->where("idPronac = p.idPronac AND idProduto <> '206' AND tpPlanilha = 'PA'");
        }

        //=============== VALOR APROVADO ======================//
        $slctVlAprovado = $this->select();
        $slctVlAprovado->setIntegrityCheck(false);
        $slctVlAprovado->from(
                        array("tbPlanilhaAprovacao"),
                        array(new Zend_Db_Expr("ROUND(SUM(qtItem*nrOcorrencia*vlUnitario),2)")),
                        "SAC.dbo"
                     );
        $slctVlAprovado->where("IdPRONAC = p.idPronac and stAtivo='S' and nrFonteRecurso=109");

        $slct->joinInner(
                            array('p2'=>'Projetos'),
                            't.idPronac = p2.idPronac',
                            array('VlSolicitado'=>"({$slctVlSolicitado})", 'VlSugerido'=>"({$slctVlSugerido})", 'VlAprovado2'=>"({$slctVlAprovado})"),
                            'SAC.dbo'
                          );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){

            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                            array('t'=>$this->_schema.'.'.$this->_name),
                            array('total'=>"count(*)")
                         );
            $slct2->joinInner(
                                array('z'=>'tbDistribuicaoProjetoComissao'),
                                't.IdPRONAC = z.idPRONAC',
                                array(),
                                'BDCORPORATIVO.scSAC'
                              );
            $slct2->joinInner(
                                array('p'=>'Projetos'),
                                't.idPronac = p.idPronac',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('d'=>'tbDistribuirParecer'),
                                'p.IdPRONAC = d.idPRONAC',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('s'=>'Situacao'),
                                'p.Situacao = s.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('a'=>'Area'),
                                'p.Area = a.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('se'=>'Segmento'),
                                'p.Segmento = se.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('n1'=>'Nomes'),
                                'z.idAgente = n1.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            $slct2->joinInner(
                                array('x'=>'Agentes'),
                                'p.CgcCpf = x.CNPJCPF',
                                array(),
                                "Agentes.dbo"
                              );
            $slct2->joinInner(
                                array('n'=>'Nomes'),
                                'x.idAgente = n.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            $slct2->joinInner(
                                array('r'=>'tbReuniao'),
                                't.idNrReuniao = r.idNrReuniao',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('o'=>'Orgaos'),
                                'd.idOrgao = o.Codigo',
                                array(),
                                "SAC.dbo"
                              );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct2->where($coluna, $valor);
            }

            //xd($slct2->__toString());
            $rs = $this->fetchAll($slct2)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //xd($slct->assemble());
        return $this->fetchAll($slct);
    }

    public function buscarProjetosVotoAlterado($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false){
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slctInterno = $this->select();
        $slctInterno->setIntegrityCheck(false);
        $slctInterno->from(
                        array("tbPlanilhaAprovacao"),
                        array(new Zend_Db_Expr("ROUND(SUM(qtItem*nrOcorrencia*vlUnitario),2)")),
                        "SAC.dbo"
                     );
        $slctInterno->where("IdPRONAC = p.idPronac and stAtivo='S' and nrFonteRecurso=109");

        $slct->from(
                        array('t'=>$this->_schema.'.'.$this->_name),
                        array('idNrReuniao', 'IdPronac', 'VlAprovado'=>"({$slctInterno})", "stAnalise")
                     );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                            't.idPronac = p.idPronac',
                            array("Pronac"=>new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"), "NomeProjeto", "Situacao", "Area", "Segmento"),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('s'=>'Situacao'),
                            'p.Situacao = s.Codigo',
                            array("DescSituacao"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array("DescArea"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array("DescSegmento"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('x'=>'Agentes'),
                            'p.CgcCpf = x.CNPJCPF',
                            array(),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('n'=>'Nomes'),
                            'x.idAgente = n.idAgente',
                            array("Proponente"=>"Descricao"),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('r'=>'tbReuniao'),
                            't.idNrReuniao = r.idNrReuniao',
                            array(),
                            "SAC.dbo"
                          );

        $slctInterno = $this->select();
        $slctInterno->setIntegrityCheck(false);
        $slctInterno->from(
                        array('pa'=>'tbPlanilhaAprovacao'),
                        array('*'),
                        'SAC.dbo'
                     );
        $slctInterno->where("pa.IdPRONAC = t.IdPronac and pa.tpPlanilha = 'SE'");
        $slctInterno->limit(1);
        $slct->where("EXISTS (?)", new Zend_Db_Expr($slctInterno));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                            array('t'=>$this->_schema.'.'.$this->_name),
                            array('total'=>"count(*)")
                         );
            $slct2->joinInner(
                                array('p'=>'Projetos'),
                                't.idPronac = p.idPronac',
                                array(),
                                'SAC.dbo'
                              );
            $slct2->joinInner(
                                array('s'=>'Situacao'),
                                'p.Situacao = s.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('a'=>'Area'),
                                'p.Area = a.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('se'=>'Segmento'),
                                'p.Segmento = se.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slct2->joinInner(
                                array('x'=>'Agentes'),
                                'p.CgcCpf = x.CNPJCPF',
                                array(),
                                "Agentes.dbo"
                              );
            $slct2->joinInner(
                                array('n'=>'Nomes'),
                                'x.idAgente = n.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            $slct2->joinInner(
                                array('r'=>'tbReuniao'),
                                't.idNrReuniao = r.idNrReuniao',
                                array(),
                                "SAC.dbo"
                              );

            $slctInterno2 = $this->select();
            $slctInterno2->setIntegrityCheck(false);
            $slctInterno2->from(
                            array('pa'=>'tbPlanilhaAprovacao'),
                            array('*'),
                            'SAC.dbo'
                         );
            $slctInterno2->where("pa.IdPRONAC = t.IdPronac and pa.tpPlanilha = 'SE'");
            $slctInterno2->limit(1);
            $slct2->where("EXISTS (?)", new Zend_Db_Expr($slctInterno2));

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct2->where($coluna, $valor);
            }

            //xd($slct2->__toString());
            $rs = $this->fetchAll($slct2)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //xd($slct->getPart(Zend_Db_Select::COLUMNS));
        //xd($slct->assemble());
        return $this->fetchAll($slct);
    }

    public function buscarProjetosEmPautaReuniaoCnic($where=array(),$order=array(), $tamanho=-1, $inicio=-1, $count=false) {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('tp' => $this->_schema . '.' . $this->_name),
                array(
                    'tp.dtEnvioPauta',
                    'tp.stEnvioPlenario',
                    'tp.stAnalise'
                )
        );
        $slct->joinInner(
                array('pr' => 'Projetos'),
                "pr.IdPRONAC = tp.IdPRONAC",
                array(
                    '(pr.AnoProjeto+pr.Sequencial) as pronac',
                    'pr.NomeProjeto',
                    'pr.IdPRONAC'
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('ar' => 'Area'),
                "pr.Area = ar.Codigo",
                array('ar.Descricao as area'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('seg' => 'Segmento'),
                "pr.Segmento = seg.Codigo",
                array('seg.Descricao as segmento'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('par' => 'Parecer'),
                "par.IdPRONAC = tp.IdPRONAC AND par.DtParecer = (SELECT TOP 1 max(DtParecer) from SAC..Parecer where IdPRONAC = pr.IdPRONAC)",
                array('par.ParecerFavoravel'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('dpc' => 'tbDistribuicaoProjetoComissao'),
                "pr.IdPRONAC = dpc.idPRONAC",
                array(),
                'scSAC'
        );
        $slct->joinInner(
                array('nm' => 'Nomes'),
                "nm.idAgente = dpc.idAgente",
                array('Descricao as nomeComponente'),
                'Agentes.dbo'
        );
        $slct->joinInner(
                array('pp' => 'PreProjeto'),
                "pr.idProjeto = pp.idPreProjeto",
                array('stPlanoAnual'),
                'SAC.dbo'
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $selectCount->from(
                            array('p'=>$this->_schema.'.'.$this->_name),
                            array('total'=>"count(*)")  
                         );
            $slctContador->joinInner(
                    array('pr' => 'Projetos'),
                    "pr.IdPRONAC = tp.IdPRONAC",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('ar' => 'Area'),
                    "pr.Area = ar.Codigo",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('seg' => 'Segmento'),
                    "pr.Segmento = seg.Codigo",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('par' => 'Parecer'),
                    "par.IdPRONAC = tp.IdPRONAC",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('dpc' => 'tbDistribuicaoProjetoComissao'),
                    "pr.IdPRONAC = dpc.idPRONAC",
                    array(),
                    'scSAC'
            );
            $slctContador->joinInner(
                    array('nm' => 'Nomes'),
                    "nm.idAgente = dpc.idAgente",
                    array(),
                    'Agentes.dbo'
            );
            $slctContador->joinInner(
                    array('pp' => 'PreProjeto'),
                    "pr.idProjeto = pp.idPreProjeto",
                    array(),
                    'SAC.dbo'
            );

            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //xd($slct->assemble());
        return $this->fetchAll($slct);
    }
    

    public function buscarProjetosVotadosCnic($where=array(),$order=array(), $tamanho=-1, $inicio=-1, $count=false) {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('tp' => $this->_schema . '.' . $this->_name),
                array(
                    'tp.dtEnvioPauta',
                    'tp.stEnvioPlenario',
                    'tp.stAnalise'
                )
        );
        $slct->joinInner(
                array('pr' => 'Projetos'),
                "pr.IdPRONAC = tp.IdPRONAC",
                array(
                    '(pr.AnoProjeto+pr.Sequencial) as pronac',
                    'pr.NomeProjeto',
                    'pr.IdPRONAC'
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('ar' => 'Area'),
                "pr.Area = ar.Codigo",
                array('ar.Descricao as area'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('seg' => 'Segmento'),
                "pr.Segmento = seg.Codigo",
                array('seg.Descricao as segmento'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('par' => 'Parecer'),
                "par.IdPRONAC = tp.IdPRONAC AND par.DtParecer = (SELECT TOP 1 max(DtParecer) from SAC..Parecer where IdPRONAC = pr.IdPRONAC)",
                array('par.ParecerFavoravel'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('dpc' => 'tbDistribuicaoProjetoComissao'),
                "pr.IdPRONAC = dpc.idPRONAC",
                array(),
                'scSAC'
        );
        $slct->joinInner(
                array('nm' => 'Nomes'),
                "nm.idAgente = dpc.idAgente",
                array('Descricao as nomeComponente'),
                'Agentes.dbo'
        );
        $slct->joinInner(
                array('pp' => 'PreProjeto'),
                "pr.idProjeto = pp.idPreProjeto",
                array('stPlanoAnual'),
                'SAC.dbo'
        );
        $slct->joinInner(
                array('cv' => 'tbConsolidacaoVotacao'),
                "pr.IdPRONAC = cv.IdPRONAC",
                array('*',
                      'resultadoPlenaria' => new Zend_Db_Expr("CASE
                                                                WHEN stAnalise = 'AS'
                                                                    THEN 'Aprovado na plen�ria'
                                                                WHEN stAnalise = 'AC'
                                                                    THEN 'Aprovado pelo componente'
                                                                WHEN stAnalise = 'IS'
                                                                    THEN 'Indeferido na plen�ria'
                                                                WHEN stAnalise = 'IC'
                                                                    THEN 'Indeferido pelo componente'
                                                                END "),
                ),'BDCORPORATIVO.scSAC'
        );
        $slct->joinInner(
                array('vt' => 'tbVotacao'),
                "pr.IdPRONAC = vt.IdPRONAC",
                array('*',
                      'seuVoto' => new Zend_Db_Expr("CASE
                                                                WHEN stVoto = 'A'
                                                                    THEN 'Aprovou'
                                                                WHEN stVoto = 'B'
                                                                    THEN 'Absteve'
                                                                WHEN stVoto = 'I'
                                                                    THEN 'Indeferiu'
                                                                END ")),
                'BDCORPORATIVO.scSAC'
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $selectCount->from(
                            array('p'=>$this->_schema.'.'.$this->_name),
                            array('total'=>"count(*)")  
                         );
            $slctContador->joinInner(
                    array('pr' => 'Projetos'),
                    "pr.IdPRONAC = tp.IdPRONAC",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('ar' => 'Area'),
                    "pr.Area = ar.Codigo",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('seg' => 'Segmento'),
                    "pr.Segmento = seg.Codigo",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('par' => 'Parecer'),
                    "par.IdPRONAC = tp.IdPRONAC",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('dpc' => 'tbDistribuicaoProjetoComissao'),
                    "pr.IdPRONAC = dpc.idPRONAC",
                    array(),
                    'scSAC'
            );
            $slctContador->joinInner(
                    array('nm' => 'Nomes'),
                    "nm.idAgente = dpc.idAgente",
                    array(),
                    'Agentes.dbo'
            );
            $slctContador->joinInner(
                    array('pp' => 'PreProjeto'),
                    "pr.idProjeto = pp.idPreProjeto",
                    array(),
                    'SAC.dbo'
            );
            $slctContador->joinInner(
                    array('cv' => 'tbConsolidacaoVotacao'),
                    "pr.IdPRONAC = cv.IdPRONAC",
                    array(),
                    'BDCORPORATIVO.scSAC'
            );
            $slctContador->joinInner(
                    array('vt' => 'tbVotacao'),
                    "pr.IdPRONAC = vt.IdPRONAC",
                    array(),
                    'BDCORPORATIVO.scSAC'
            );

            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //xd($slct->assemble());
        return $this->fetchAll($slct);
    }
    
    public function buscarProjetosTermoAprovacao($where=array(),$order=array(), $tamanho=-1, $inicio=-1, $count=false) {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('tp' => $this->_schema . '.' . $this->_name),
                array(
                    'tp.IdPRONAC',
                    'tp.stAnalise'
                )
        );
        $slct->joinInner(
                array('pr' => 'Projetos'),
                "pr.IdPRONAC = tp.IdPRONAC",
                array(
                    '(pr.AnoProjeto+pr.Sequencial) as pronac',
                    'SAC.dbo.fnTotalAprovadoProjeto(pr.AnoProjeto,pr.Sequencial) AS AprovadoReal',
                    'pr.NomeProjeto',
                    'pr.Situacao',
                    'pr.Area',
                    'pr.Orgao',
                    'TABELAS.dbo.fnCodigoOrgaoEstrutura(pr.orgao, 1) AS orgaoSuperior',
                    'pr.DtProtocolo',
                    new Zend_Db_Expr('SAC.dbo.fnDtAprovacao(pr.AnoProjeto,pr.Sequencial) as DtAprovacao')
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('a' => 'Area'),
                "a.Codigo = pr.Area",
                array('a.Descricao as descricaoArea'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('par' => 'Parecer'),
                "par.IdPRONAC = tp.IdPRONAC AND par.stAtivo = 1",
                array('par.TipoParecer',
                      'par.ParecerFavoravel',
                      'par.ResumoParecer'),
                "SAC.dbo"
        );
        $slct->joinInner(
                array('r' => 'tbReuniao'),
                'tp.idNrReuniao = r.idNrReuniao',
                array(
                    'r.NrReuniao',
                    'r.DtInicio',
                    'r.DtFinal',
                    'DtAssinatura' => New Zend_Db_Expr("CASE DATEPART(DW, r.DtFinal) 
                                                             WHEN 6 THEN DATEADD(D,3,r.DtFinal) -- SEXTA-FEIRA (ADICIONA TRES DIAS)
                                                             WHEN 7 THEN DATEADD(D,2,r.DtFinal) -- SABADO (ADICIONA DOIS DIAS)
                                                             ELSE  DATEADD(D,1,r.DtFinal) -- OTROS DIAS DA SEMNA (ADICIONA UM DIA)
                                                             END "),
                ),
                'SAC.dbo'
        );
        $slct->joinLeft(
                array('cv' => 'tbConsolidacaoVotacao'),
                "cv.IdPRONAC = tp.IdPRONAC and cv.IdNrReuniao = tp.IdNrReuniao",
                array('CAST(cv.dsConsolidacao as TEXT) as dsConsolidacao'),
                "BDCORPORATIVO.scSAC"
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $selectCount->from(
                            array('tp'=>$this->_schema.'.'.$this->_name),
                            array('total'=>"count(*)")  
                         );
            $slctContador->joinInner(
                    array('pr' => 'Projetos'),
                    "pr.IdPRONAC = tp.IdPRONAC",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('par' => 'Parecer'),
                    "par.IdPRONAC = tp.IdPRONAC AND par.stAtivo = 1",
                    array(),
                    "SAC.dbo"
            );
            $slctContador->joinInner(
                    array('r' => 'tbReuniao'),
                    'tp.idNrReuniao = r.idNrReuniao',
                    array(),
                    'SAC.dbo'
            );
            $slctContador->joinLeft(
                    array('cv' => 'tbConsolidacaoVotacao'),
                    "cv.IdPRONAC = tp.IdPRONAC and cv.IdNrReuniao = tp.IdNrReuniao",
                    array(),
                    "BDCORPORATIVO.scSAC"
            );
            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        //adicionando linha order ao select
        $slct->order($order);
        $slct->order(3);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //xd($slct->assemble());
        return $this->fetchAll($slct);
    }

    public function parecerDoComponenteComissao($idPronac) {

        $select =  new Zend_Db_Expr("
                SELECT p.idPronac,x.AnoProjeto+x.Sequencial as PRONAC,x.NomeProjeto ,pa.stAtivo,pa.idTipoAgente,n.usu_nome ,pa.ParecerFavoravel,pa.ResumoParecer,p.stEnvioPlenario,
                       r.NrReuniao,r.DtFinal,
                       round((Select sum(qtItem * nrOcorrencia * vlUnitario) From tbPlanilhaAprovacao y where y.idPronac = x.idPronac and y.stAtivo = 'S'),2) AS valor
                FROM BDCORPORATIVO.scSAC.tbPauta p
                INNER JOIN Parecer pa ON (p.IdPRONAC = pa.IdPRONAC)
                INNER JOIN Projetos x ON (x.IdPRONAC = pa.IdPRONAC)
                INNER JOIN tbReuniao r ON (p.idNrReuniao = r.idNrReuniao)
                INNER JOIN Tabelas..Usuarios n ON (n.usu_codigo = pa.Logon)
                WHERE pa.idTipoAgente = 6 AND p.idPronac = $idPronac ");
        try {
            $db = Zend_Registry::get('db');
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }
    
    public function buscaProjetosAprovados($idNrReuniao) {
        
        //PROJETOS NORMAIS DA CNIC
        $slct1 = $this->select();
        $slct1->setIntegrityCheck(false);
        $slct1->from(
            array('a' => $this->_schema . '.' . $this->_name),
            array(
                new Zend_Db_Expr("'An�lise Inicial' AS TipoAprovacao,'' AS Tipo,b.IdPRONAC,b.AnoProjeto+b.Sequencial as PRONAC, b.NomeProjeto"),
                new Zend_Db_Expr("(SELECT COUNT(d.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao d WHERE d.stVoto = 'A' and d.idPronac = a.IdPRONAC) as QtdeVotoAprovacao"),
                new Zend_Db_Expr("(SELECT COUNT(e.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao e WHERE e.stVoto = 'B' and e.idPronac = a.IdPRONAC) as QtdeVotoAbstencao"),
                new Zend_Db_Expr("(SELECT COUNT(f.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao f WHERE f.stVoto = 'I' and f.idPronac = a.IdPRONAC) as QtdeVotoIndeferimento"),
                'c.dsConsolidacao'
            )
        );
        $slct1->joinInner(
            array('b' => 'Projetos'), "a.IdPRONAC = b.IdPRONAC",
            array(), "SAC.dbo"
        );
        $slct1->joinInner(
            array('c' => 'tbConsolidacaoVotacao'), "b.IdPRONAC = c.idPRONAC",
            array(), "BDCORPORATIVO.scSAC"
        );
        $slct1->where('a.stEnvioPlenario = ?', 'S');
        $slct1->where('a.idNrReuniao = ?', $idNrReuniao);
        
        
        //PROJETOS DE RECURSOS
        $slct2 = $this->select();
        $slct2->setIntegrityCheck(false);
        $slct2->from(
            array('a' => 'tbRecurso'),
            array(
                new Zend_Db_Expr("'Recurso' AS TipoAprovacao,'' AS Tipo,b.IdPRONAC,b.AnoProjeto+b.Sequencial as PRONAC, b.NomeProjeto"),
                new Zend_Db_Expr("(SELECT COUNT(d.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao d WHERE d.stVoto = 'A' and d.idPronac = a.IdPRONAC) as QtdeVotoAprovacao"),
                new Zend_Db_Expr("(SELECT COUNT(e.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao e WHERE e.stVoto = 'B' and e.idPronac = a.IdPRONAC) as QtdeVotoAbstencao"),
                new Zend_Db_Expr("(SELECT COUNT(f.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao f WHERE f.stVoto = 'I' and f.idPronac = a.IdPRONAC) as QtdeVotoIndeferimento"),
                'c.dsConsolidacao'
            ), 'SAC.dbo'
        );
        $slct2->joinInner(
            array('b' => 'Projetos'), "a.IdPRONAC = b.IdPRONAC",
            array(), "SAC.dbo"
        );
        $slct2->joinInner(
            array('c' => 'tbConsolidacaoVotacao'), "b.IdPRONAC = c.idPRONAC",
            array(), "BDCORPORATIVO.scSAC"
        );
        $slct2->where('a.siRecurso = ?', 8);
        $slct2->where('a.idNrReuniao = ?', $idNrReuniao);
        $slct2->where('a.stEstado = ?', 0);
        
        
        //PROJETOS DE READEQUA��O
        $slct3 = $this->select();
        $slct3->setIntegrityCheck(false);
        $slct3->from(
            array('a' => 'tbReadequacao'),
            array(
                new Zend_Db_Expr("'Readequa��o' AS TipoAprovacao,d.dsReadequacao AS Tipo,b.IdPRONAC,b.AnoProjeto+b.Sequencial AS PRONAC, b.NomeProjeto"),
                new Zend_Db_Expr("(SELECT COUNT(d.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao d WHERE d.stVoto = 'A' and d.idPronac = a.IdPRONAC and d.tpTipoReadequacao = a.idTipoReadequacao) as QtdeVotoAprovacao"),
                new Zend_Db_Expr("(SELECT COUNT(e.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao e WHERE e.stVoto = 'B' and e.idPronac = a.IdPRONAC and e.tpTipoReadequacao = a.idTipoReadequacao) as QtdeVotoAbstencao"),
                new Zend_Db_Expr("(SELECT COUNT(f.stVoto) FROM BDCORPORATIVO.scSAC.tbVotacao f WHERE f.stVoto = 'I' and f.idPronac = a.IdPRONAC and f.tpTipoReadequacao = a.idTipoReadequacao) as QtdeVotoIndeferimento"),
                'c.dsConsolidacao'
            ), 'SAC.dbo'
        );
        $slct3->joinInner(
            array('b' => 'Projetos'), "a.IdPRONAC = b.IdPRONAC",
            array(), "SAC.dbo"
        );
        $slct3->joinInner(
            array('c' => 'tbConsolidacaoVotacao'), "b.IdPRONAC = c.idPRONAC AND a.idTipoReadequacao = c.tpTipoReadequacao",
            array(), "BDCORPORATIVO.scSAC"
        );
        $slct3->joinInner(
            array('d' => 'tbTipoReadequacao'), "a.idTipoReadequacao = d.idTipoReadequacao",
            array(), "SAC.dbo"
        );
        $slct3->where('a.siEncaminhamento = ?', 8);
        $slct3->where('a.idNrReuniao = ?', $idNrReuniao);
        $slct3->where('a.stEstado = ?', 0);
        
        $slctUnion = $this->select()
                            ->union(array('('.$slct1.')', '('.$slct2.')', '('.$slct3.')'))
                            ->order('3');

        //xd($slctUnion->assemble());
        return $this->fetchAll($slctUnion);
    }
    
}
?>