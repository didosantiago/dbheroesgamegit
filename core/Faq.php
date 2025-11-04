<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Faq
 *
 * @author Felipe Faciroli
 */
class Faq {
    public function getList(){         
        $sql = "SELECT * FROM faq WHERE status = 1 ORDER BY id ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<li class="questions">
                        <h3>'.$value->pergunta.'</h3>
                        <p>'.$value->resposta.'</p>
                     </li>';
        }
        
        echo $row;
    }
}
