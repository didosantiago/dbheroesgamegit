<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Administrar
 *
 * @author Felipe Faciroli
 */
class Administrar {
    public function getListGuerreiros(){
        $core = new Core();
        
        $sql = "SELECT * FROM personagens ORDER BY nome ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            if($value->liberado == 0){
                $ativo = 'inativo';
            } else {
                $ativo = '';
            }
            
            $row .= '<li class="adm-personagem '.$ativo.'">
                        <a href="'.BASE.'personagens/edit/'.$value->id.'">
                            <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                            <div class="info">
                                <h3>'.$value->nome.'</h3>
                                <span class="raca"><strong>Raça:</strong>'.$value->raca.'</span>
                                <span class="hp"><strong>HP:</strong>'.$value->hp.'</span>
                                <span class="mana"><strong>KI:</strong>'.$value->mana.'</span>
                                <span class="energia"><strong>Energia:</strong>'.$value->energia.'</span>
                            </div>
                        </a>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListPalavrasOfensivas(){
        $core = new Core();
        
        $sql = "SELECT * FROM palavras_bloqueadas ORDER BY palavra ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $row .= '<li>
                        <a href="'.BASE.'palavras/edit/'.$value->id.'">
                            <h3>'.$value->palavra.'</h3>
                            <i class="fas fa-edit"></i>
                        </a>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getJogadoresPorPersonagens(){
        $sql = "SELECT * FROM personagens WHERE liberado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $sql = "SELECT count(*) as total FROM usuarios_personagens WHERE idPersonagem = $value->id";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $quantidade = $stmt->fetch();
            
            $row .= '<li>
                        <img src="'.BASE.'assets/cards/'.$value->foto.'" alt="'.$value->nome.'" />
                        <span>'.$quantidade->total.'</span>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListVisitantesOnline($pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $sql = "SELECT count(*) as total "
             . "FROM usuarios_personagens as up "
             . "INNER JOIN usuarios_monitoramento as um ON um.idPersonagem = up.id "
             . "INNER JOIN usuarios as u ON u.id = up.idUsuario ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $qtd = $stmt->fetch();
        $counter = $qtd->total;     
        
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT up.*, u.nome as nome_usuario, u.email as email_usuario "
             . "FROM usuarios_personagens as up "
             . "INNER JOIN usuarios_monitoramento as um ON um.idPersonagem = up.id "
             . "INNER JOIN usuarios as u ON u.id = up.idUsuario "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<tr>
                            <td>'.$value->nome.'</td>
                            <td>'.$value->nome_usuario.'</td>
                            <td>'.$value->email_usuario.'</td>
                            <td>'.$core->dataBR($value->data_cadastro).'</td>
                            <td>'.$value->gold_total.'</td>
                            <td>'.$value->nivel.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="6" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="6" class="not">Nenhum Guerreiro Online.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function getListTransacoes($pc, $qtd_resultados){
        $core = new Core();
        
        $mes = date('m');
        $ano = date('Y');
        
        $sql = "SELECT t.*, u.nome, u.email "
             . "FROM transacoes as t "
             . "INNER JOIN usuarios as u ON u.id = t.idUsuario "
             . "WHERE MONTH(t.data) = '$mes' "
             . "AND YEAR(t.data) = '$ano' "
             . "AND t.status = 3 ";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $qtd = $stmt->fetch();
        $counter = $qtd->total;     
        
        //Paginando os Resultados
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT t.*, u.nome, u.email "
             . "FROM transacoes as t "
             . "INNER JOIN usuarios as u ON u.id = t.idUsuario "
             . "WHERE MONTH(t.data) = '$mes' "
             . "AND YEAR(t.data) = '$ano' "
             . "AND t.status = 3 "
             . "LIMIT " . $inicio . ',' . $qtd_resultados;
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $row .= '<tr>
                            <td>'.$value->nome.'</td>
                            <td>'.$value->email.'</td>
                            <td>'.$core->dataBR($value->data).'</td>
                            <td>'.$core->formataMoeda($value->valor).'</td>
                            <td>'.$value->coins.'</td>
                         </tr>';
            }
            
            // Mostra Navegador da Paginação
            $row .= '<tr>'
                   . '<td colspan="5" style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                 . '</tr>'; 
            
        } else {
            $row .= '<tr>
                        <td colspan="5" class="not">Nenhuma Transação Recebida.</td>
                     </tr>';
        }
        
        echo $row;
    }
    
    public function getListVideosDestaque(){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_videos WHERE destaque = 1 ORDER BY id DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<li>
                        <iframe width="1240" height="700" src="https://www.youtube.com/embed/'.$value->url.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allownetworking="internal"></iframe>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getListVideos(){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_videos ORDER BY id DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<li>
                        <iframe width="390" height="270" src="https://www.youtube.com/embed/'.$value->url.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allownetworking="internal"></iframe>
                     </li>';
        }
        
        echo $row;
    }
    
    public function existsEnquete(){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_enquetes WHERE votacao = 1 AND status = 1 ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getEnquete(){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_enquetes WHERE votacao = 1 AND status = 1 ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $enquete = $stmt->fetch();
        
        return $enquete;
    }
    
    public function getOptionsEnquete($idEnquete){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_enquetes_opcoes WHERE idEnquete = $idEnquete ORDER BY ordem ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $opcoes = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($opcoes as $key => $value) {
            $row .= '<label for="enquete_'.$value->id.'">
                        <input type="radio" id="enquete_'.$value->id.'" name="votar_enquete" value="'.$value->id.'" required />
                        <span>'.$value->opcao.'</span>
                     </label>';
        }
        
        return $row;
    }
    
    public function getPorcentagensEnquete($idEnquete){
        $core = new Core();
        
        $sql = "SELECT * FROM adm_enquetes_opcoes WHERE idEnquete = $idEnquete ORDER BY ordem ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $opcoes = $stmt->fetchAll();
        
        $sql = "SELECT sum(votos) as total FROM adm_enquetes_opcoes WHERE idEnquete = $idEnquete";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $totais = $stmt->fetch();
        
        $row = '';
        
        foreach ($opcoes as $key => $value) {
            $total = $totais->total;
            
            if($total != 0){
              $porcentagem = intval(($value->votos / $total) * 100);
            } else {
              $porcentagem = 0;
            }
            
            $row .= '<li>
                        <span>'.$value->opcao.'</span>
                        <div class="meter animate roxo">
                            <em>'.$porcentagem.'%</em>
                            <span style="width: '.$porcentagem.'%"><span></span></span>
                        </div>
                     </li>';
        }
        
        return $row;
    }
}
