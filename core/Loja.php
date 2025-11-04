<?php

/**
 * Description of Loja
 *
 * @author Felipe Faciroli
 */
class Loja {
    public function getLoja(){
        $sql = "SELECT * FROM adm_loja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        return $item;
    }

    public function getPrecoFoto($foto, $valor, $modulo){
        if($modulo == 1){
            $sql = "SELECT * FROM personagens_fotos WHERE foto = '$foto' AND free = 0";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();

            if($item->raridade == 1){
                $preco = 4;
            } else if($item->raridade == 2){
                $preco = 6;
            } else if($item->raridade == 3){
                $preco = 8;
            } else if($item->raridade == 4){
                $preco = 10;
            } else if($item->raridade == 10){
                $preco = 15;
            }
        } else {
            $preco = $valor;
        }

        $row = '<div class="valor">
                    <img src="'.BASE.'assets/icones/coin.png" />';
        if(!empty($valor) && $valor < $preco){
            $row .= '<span class="preco_de">De: <em>'.$preco.'</em></span>';
            $row .= '<span class="preco_por">Por: <em>'.$valor.'</em></span>';
        } else {
            $row .= '<span class="preco_por">'.$preco.'</span>';
        }
        $row .= '</div>';

        return $row;
    }

    public function getClassRarirade($foto){
        $sql = "SELECT * FROM personagens_fotos WHERE foto = '$foto' AND free = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        $tipo = $item->raridade;

        if($tipo == 1){
            $classe = 'green';
        } else if($tipo == 2){
            $classe = 'blue';
        } else if($tipo == 3){
            $classe = 'purple';
        } else if($tipo == 4){
            $classe = 'orange';
        }

        return $classe;
    }

    public function getValor($foto, $valor, $modulo){
        $sql = "SELECT * FROM personagens_fotos WHERE foto = '$foto' AND free = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        if($item->raridade == 1){
            $preco = 4;
        } else if($item->raridade == 2){
            $preco = 6;
        } else if($item->raridade == 3){
            $preco = 8;
        } else if($item->raridade == 4){
            $preco = 10;
        } else if($item->raridade == 10){
            $preco = 15;
        }

        if($modulo == 1){
            if(!empty($valor) && $valor < $preco){
                return $valor;
            } else {
                return $preco;
            }
        } else {
            return $valor;
        }
    }

    public function getNomeFoto($idBoneco, $modulo, $nomeProduto){
        if($modulo == 1){
            $sql = "SELECT * FROM personagens WHERE id = $idBoneco";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();

            return $item->nome;
        } else {
            return $nomeProduto;
        }
    }

    public function marcarFotoLoja($idLoja){
        if (empty($idLoja)) {
            return;
        }
        $core = new Core();

        $campos = array(
            'loja' => 0
        );

        $where = 'id is not null';

        $core->update('adm_loja_itens', $campos, $where);

        $todosAnuncios = $core->getDados('adm_loja_produtos');

        $sql = "SELECT * FROM adm_loja_produtos WHERE id = $idLoja";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        if (!$item) {
            return;
        }

        // Update only if posicao_N is not empty
        $this->atualizaFotoLoja($item->posicao_1);
        $this->atualizaFotoLoja($item->posicao_2);
        $this->atualizaFotoLoja($item->posicao_3);
        $this->atualizaFotoLoja($item->posicao_4);
        $this->atualizaFotoLoja($item->posicao_5);
        $this->atualizaFotoLoja($item->posicao_6);
        $this->atualizaFotoLoja($item->posicao_7);
        $this->atualizaFotoLoja($item->posicao_8);
    }

    public function atualizaFotoLoja($id){
        if (empty($id)) {
            return;
        }
        $core = new Core();

        $campos = array(
            'loja' => 1
        );

        $where = 'id = '.$id;

        $core->update('adm_loja_itens', $campos, $where);
    }

    public function marcarFotoLoja2($id){
        $core = new Core();

        $sql = "SELECT * FROM adm_loja_produtos";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();

        $status = 0;
        $loja = 0;

        if($item->posicao_1 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_2 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_3 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_4 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_5 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_6 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_7 == $id){ $status = 1; $loja = 1; }
        if($item->posicao_8 == $id){ $status = 1; $loja = 1; }

        if($loja == 1){
            $status = 1;
        } else {
            if($item->posicao_9 == $id){ $status = 1; }
            if($item->posicao_10 == $id){ $status = 1; }
            if($item->posicao_11 == $id){ $status = 1; }
            if($item->posicao_12 == $id){ $status = 1; }
            if($item->posicao_13 == $id){ $status = 1; }
            if($item->posicao_14 == $id){ $status = 1; }
            if($item->posicao_15 == $id){ $status = 1; }
            if($item->posicao_16 == $id){ $status = 1; }
        }

        $campos = array(
            'loja' => $status
        );

        $where = 'id = '.$id;

        $core->update('adm_loja_itens', $campos, $where);
    }

    public function getDadosItensBau($idBau){
        $sql = "SELECT * FROM itens_bau WHERE idBau = $idBau";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $itens = $stmt->fetchAll();

        $row = '';

        foreach ($itens as $key => $value) {
            $sql = "SELECT * FROM itens WHERE id = $value->idItem";
            $stmt = DB::prepare($sql);
            $stmt->execute();
            $item = $stmt->fetch();

            $row .= '<div class="status-item">';
            $row .= '<div class="foto-item">
                        <img src="'.BASE.'assets/'.$item->foto.'" />
                     </div>';

            $row .= '<h5>'.$item->nome.'</h5>';

            if($item->tipo == 1 || $item->tipo == 3 || $item->tipo == 4){
                $row .= '<h4>Item Consumível</h4>';
                $percent = '% de recuperação';
            } else {
                $percent = '';
            }

            if($item->hp > 0){
                $row .= '<p><strong>HP:</strong>+ '.$item->hp.$percent.'</p>';
            }

            if($item->mana > 0){
                $row .= '<p><strong>KI:</strong>+ '.$item->mana.$percent.'</p>';
            }

            if($item->energia > 0){
                $row .= '<p><strong>Energia:</strong>+ '.$item->energia.'</p>';
            }

            if($item->forca > 0){
                $row .= '<p><strong>Força:</strong>+ '.$item->forca.'</p>';
            }

            if($item->agilidade > 0){
                $row .= '<p><strong>Agilidade:</strong>+ '.$item->agilidade.'</p>';
            }

            if($item->habilidade > 0){
                $row .= '<p><strong>Habilidade:</strong>+ '.$item->habilidade.'</p>';
            }

            if($item->resistencia > 0){
                $row .= '<p><strong>Resistência:</strong>+ '.$item->resistencia.'</p>';
            }

            if($item->sorte > 0){
                $row .= '<p><strong>Sorte:</strong>+ '.$item->sorte.'</p>';
            }
            $row .= '</div>';
        }

        return $row;
    }

    public function getTipoProduto($modulo){
        if($modulo == 1){
            $texto = 'Foto de Perfil';
        } else if($modulo == 2){
            $texto = 'Troca de Nome';
        } else if($modulo == 3){
            $texto = 'Item do Jogo';
        }

        return $texto;
    }
}