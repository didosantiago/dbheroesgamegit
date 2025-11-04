<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Torneio
 *
 * @author Felipe Faciroli
 */
class Torneio {
    public function getList($nivel){        
        $level = $nivel + 3;
        $sql = "SELECT * FROM guerreiros_arena WHERE nivel <= $level ORDER BY nivel DESC LIMIT 6";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        $total = 5;
        $count = 0;
        $preenchido = false;
        $completo = false;
        $lista_guerreiros = array();
        
        foreach ($item as $key => $value) {
            array_push($lista_guerreiros, $value->id);
        }
        
        $sql = "SELECT g.*, g.id as idguerreiro, p.* "
            . "FROM guerreiros_arena as g "
            . "INNER JOIN guerreiros as p ON p.id = g.idGuerreiro WHERE g.id in(".implode(",", array_map('intval', $lista_guerreiros)).") "
            . "ORDER BY g.id ASC ";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $guerreiros = $stmt->fetchAll();

        foreach ($guerreiros as $key2 => $value2) {
            $count += 1;

            if($count <= 6){
                if($completo == false){
                    if($value2->nivel <= $nivel && $preenchido == false){
                        $row .= '<li class="guerreiro">
                                    <img src="'.BASE.'assets/guerreiros/'.$value2->foto.'" alt="'.$value2->nome.'" />
                                    <div class="info">
                                        <h3><strong>'.$value2->nome.'</strong> está pronto para a batalha</h3>
                                        <span class="nivel">Nível: <strong>'.$value2->nivel.'</strong></span>
                                        <span class="exp">Exp: <strong>'.$value2->exp.'</strong></span>
                                        <a href="'.BASE.'npc/'.$value2->idguerreiro.'" class="bts-form"><i class="far fa-hand-rock"></i> Atacar</a>
                                    </div>
                                 </li>';
                    } else {
                        $preenchido = true;
                        $completo = true;
                    }

                    if($preenchido == true && $completo == true){
                        $row .= '<li class="guerreiro guerreiro-bloqueado">
                                    <img src="'.BASE.'assets/guerreiros/'.$value2->foto.'" alt="'.$value2->nome.'" />
                                    <div class="info">
                                        <h3><strong>'.$value2->nome.'</strong> ainda não está liberado</h3>
                                        <span class="nivel">Nível: <strong>'.$value2->nivel.'</strong></span>
                                        <span class="exp">Exp: <strong>'.$value2->exp.'</strong></span>
                                        <a href="javascript:void(0);" class="bts-form"><i class="far fa-hand-rock"></i> Atacar</a>
                                    </div>
                                 </li>';
                    }
                }
            }
        }
        
        echo $row;
    }
}
