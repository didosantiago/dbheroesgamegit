<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mercado
 *
 * @author Felipe Faciroli
 */
class Mercado {
    public function getProdutos(){
        $core = new Core();
        
        $sql = "SELECT * FROM produtos WHERE status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                $desconto = '';
                
                if($value->desconto > 0){
                    $desconto = '<div class="flag-desconto">
                                    '.$value->desconto.'%
                                 </div>';
                }
                
                $row .= '<li>
                            <img src="'.BASE.'assets/'.$value->foto.'" alt="'.$value->nome.'" />
                            <h3>'.$value->nome.'</h3>
                            <span class="valor">'.$value->coins.'</span>
                            <span class="sigla">Coins</span>
                            '.$desconto.'
                            <a href="'.BASE.'produto/'.$value->id.'" class="bts-form" dataid="'.$value->id.'">Detalhes</a>
                         </li>';
            }
        }
        
        echo $row;
    }
    
    public function getDadosProduto($idProduto){        
        $sql = "SELECT * FROM produtos WHERE id = $idProduto";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        return $item;
    }
    
    public function getfotoItens($idBau){        
        $sql = "SELECT i.* "
             . "FROM itens_bau as ib "
             . "INNER JOIN itens as i ON i.id = ib.idItem "
             . "WHERE ib.idBau = $idBau";
        
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<li>
                        <img src="'.BASE.'assets/'.$value->foto.'" alt="'.$value->nome.'" />
                     </li>';
        }
        
        echo $row;
    }
}
