<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Noticias
 *
 * @author Felipe Faciroli
 */
class Noticias {
    public function getList($perfil, $pc, $qtd_resultados){
        $core = new Core();
        
        //Paginando os Resultados
        $counter = $core->counterRegisters("noticias");
        $pager = new Paginator();
        $inicio = $pager->inicio($pc, $counter, $qtd_resultados);
        $tp = $counter / $qtd_resultados;
        
        $sql = "SELECT * FROM noticias ORDER BY id DESC LIMIT " . $inicio . ',' . $qtd_resultados;
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<div class="news">';
                        $row .= '<div class="info">
                            <h3>'.$value->titulo.'</h3>
                            <span class="publicado-por">Publicado em '.$core->dataTimeBR($value->data_hora).'</span>';
                            if($value->foto != ''){
                                $row .= '<img src="'.BASE.$value->foto.'" alt="DB Heroes - Notícias" />';
                            }
                            $row .= '<div class="descricao">'.$value->descricao.'</div>';
                            if($perfil == 3){
                                $row .= '<a href="'.BASE.'noticias/edit/'.$value->id.'">[Editar]</a>';
                            }
                $row .= '</div>
                     </div>';
        }
        
        // Mostra Navegador da Paginação
            $row .= '<table class="lista-geral" style="width: 95%; margin: 0 auto;">'
                    . '<tbody>'
                        . '<tr style="background: #FFF;">'
                            . '<td style="test-align: center;">'.$pager->paginar($pc, $tp).'</td>'
                        . '</tr>'
                    . '</tbody>'
                  . '</table>'; 
        
        echo $row;
    }
    
    public function getLatestNews(){
        $core = new Core();
        
        $sql = "SELECT * FROM noticias WHERE status = 1 ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            if($value->foto != ''){
                $foto = $value->foto;
                $classe = 'img';
            }
            
            $saida = strip_tags($value->descricao);
            
            $row .= '<div class="news">
                        <div class="info '.$classe.'">
                            <h3>'.$value->titulo.'</h3>
                            <span>Publicado em '.$core->dataTimeBR($value->data_hora).'</span>
                            <div class="descricao">'.$saida.'</div>
                        </div>';
                        if($value->foto != ''){
                            $row .= '<img src="'.$foto.'" alt="'.$value->titulo.'" />';
                        }
                     $row .= '</div>';
        }
        
        echo $row;
    }
    
    public function getLatestNewsTwo(){
        $core = new Core();
        
        $sql = "SELECT * FROM noticias WHERE status = 1 ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $total = $stmt->fetch();
        
        $sql = "SELECT * FROM noticias WHERE id != $total->id AND status = 1 ORDER BY id DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            
            $saida = strip_tags($value->descricao);
            
            $row .= '<li class="nf">
                        <img src="'.BASE.'assets/bg-ultimos-avisos.jpg" />
                        <h4>'.$value->titulo.'</h4>
                        <p>'.$saida.'</p>
                     </li>';
        }
        
        echo $row;
    }
}
