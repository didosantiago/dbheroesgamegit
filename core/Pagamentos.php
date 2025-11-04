<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pagamentos
 *
 * @author Felipe Faciroli
 */
class Pagamentos extends Usuarios{
    
    public function saveOrder($user, $tipo, $idAutomovel){
        if($tipo == 1){
            $descricao = 'ADS Semanal';
            $valor = 8;
            $idProduto = 5;
        } else if($tipo == 2){
            $descricao = 'ADS quinzenal';
            $valor = 15.00;
            $idProduto = 6;
        } else {
            $descricao = 'ADS Mensal';
            $valor = 25.00;
            $idProduto = 7;
        }
        
        $data = date('Y-m-d');
        
        $sql = "INSERT INTO transacoes(idUser, data_cadastro, status, idReferencia, tipo, valor, idProduto) "
                . "VALUES('$user', '$data', 1, '$idAutomovel', 'automovel', '$valor', '$idProduto')";

        $stmt = DB::prepare($sql);
        $stmt->execute();
    }
    
    public function getLastOrder($idUsuario, $idPersonagem){ 
        $sql = "SELECT * FROM transacoes WHERE idUsuario = '$idUsuario' AND idPersonagem = '$idPersonagem' ORDER BY id DESC LIMIT 1";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $pedido = $stmt->fetch();
        
        return $pedido;
    }
    
    public function getOrder($idOrder){        
        $sql = "SELECT * FROM transacoes WHERE id = '$idOrder'";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $pedido = $stmt->fetch();
        
        return $pedido;
    }
    
    public function updateOrder($idOrder, $status){
        $core = new Core();
        
        $sql = "SELECT * FROM transacoes WHERE id = $idOrder";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacao = $stmt->fetch();
        
        $sql = "SELECT * FROM usuarios WHERE id = $transacao->idUsuario";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        if($status == 3){
            $coins_atualizado = $usuario->coins + $transacao->coins;
            
            if($transacao->valor >= 10){
                $campos = array(
                    'vip' => '1',
                    'coins' => $coins_atualizado
                );
            } else {
                
                $campos = array(
                    'coins' => $coins_atualizado
                );
            }

            $where = 'id="'.$transacao->idUsuario.'"';

            $core->update('usuarios', $campos, $where);
        }
        
        $sql = "UPDATE transacoes SET status = $status WHERE id = $idOrder";
        $stmt = DB::prepare($sql);
        $stmt->execute();
    }

    public function setPayment($descricao, $idUsuario, $idPersonagem){
        
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $this->getUserInfoByID($idUsuario);
        
        $pedido = $this->getLastOrder($idUsuario, $idPersonagem);
        
        $preco = number_format((float)$pedido->valor, 2, '.', '');;
        
        if($config->PAGSEGURO_ENV == 'sandbox'){
            $data['token'] = $config->PAGSEGURO_TOKEN_SANDBOX;
        } else{
            $data['token'] = $config->PAGSEGURO_TOKEN_PRODUCTION;
        }
        $data['email'] = $config->PAGSEGURO_EMAIL;
        $data['currency'] = 'BRL';
        $data['itemId1'] = '1';
        $data['itemQuantity1'] = '1';
        $data['itemDescription1'] = 'COMPRA DE  '.intval($preco).' COINS DBHEROES RPG';
        $data['itemAmount1'] = $preco;
        $data['reference'] = $pedido->id;

        $data = http_build_query($data);

        if($config->PAGSEGURO_ENV == 'sandbox'){
            $url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout';
        } else{
            $url = 'https://ws.pagseguro.uol.com.br/v2/checkout';
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $xml = curl_exec($curl);
        curl_close($curl);

        $xml = simplexml_load_string($xml);
        
        $campos = array(
            'transaction_id' => $xml->code
        );

        $where = 'id="'.$pedido->id.'"';
        
        $core->update('transacoes', $campos, $where);
                
        echo $xml->code;
    }
    
    public function getNotification($code){
        
        //STATUS TRANSAÇÕES
        /*
         * 1 - AGUARDANDO PAGAMENTO
         * 2 - EM ANÁLISE
         * 3 - PAGA
         * 4 - DISPONÍVEL
         * 5 - EM DISPUTA
         * 6 - DEVOLVIDA
         * 7 - CANCELADA
        */
        
        $core = new Core();
        $config = $core->getConfiguracoes();
        
        $notificationCode = preg_replace('/[^[:alnum:]-]/','',$code);
        
        if($config->PAGSEGURO_ENV == 'sandbox'){
            $data['token'] = $config->PAGSEGURO_TOKEN_SANDBOX;
        } else{
            $data['token'] = $config->PAGSEGURO_TOKEN_PRODUCTION;
        }

        $data['email'] = $config->PAGSEGURO_EMAIL;

        $data = http_build_query($data);

        if($config->PAGSEGURO_ENV == 'sandbox'){
            $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;
        } else{
            $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $xml = curl_exec($curl);
        curl_close($curl);

        $xml = simplexml_load_string($xml);

        $reference = $xml->reference;
        $status = $xml->status;

        if($reference && $status){
            $rs_pedido = $this->getOrder($reference);

            if($rs_pedido->id){
               $this->updateOrder($reference, $status);
            }
        }
    }
    
    public function getExistsTransactionAproved($idUsuario){
        $core = new Core();
        
        $sql = "SELECT * FROM transacoes WHERE idUsuario = $idUsuario AND status = 3 AND pre_order = 0 AND visualizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    
    public function getListAproved($idUsuario){
        $core = new Core();
        
        $sql = "SELECT * FROM transacoes WHERE idUsuario = $idUsuario AND status = 3 AND visualizado = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
                $row .= '<div class="avisos-user transaction-aproved">
                            <span>Sua Transação no valor de <strong>'.$core->formataMoeda($value->valor).'</strong> foi aprovada, foram adicionados <strong>+'.$value->coins.'</strong> coins em sua conta. Veja em suas transações </span>
                            <a class="bts-form" href="'.BASE.'transacoes">Visualizar</a>
                         </div>';
        }
        
        echo $row;
    }
    
    public function getMyTransactions($idUsuario){
        $user = new Usuarios();
        $core = new Core();

        $status = '';
        
        $sql = "SELECT * FROM transacoes WHERE idUsuario = $idUsuario AND pre_order = 0";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            $item = $stmt->fetchAll();

            foreach ($item as $key => $value) {
                
                $item_aproved = '';
                
                //STATUS TRANSAÇÕES
                /*
                 * 1 - AGUARDANDO PAGAMENTO
                 * 2 - EM ANÁLISE
                 * 3 - PAGA
                 * 4 - DISPONÍVEL
                 * 5 - EM DISPUTA
                 * 6 - DEVOLVIDA
                 * 7 - CANCELADA
                */
                
                if($value->status == 3){
                    $item_aproved = 'aproved';
                    $status = 'Pagamento Aprovado';
                } else if($value->status == 1){
                    $item_aproved = 'pending';
                    $status = 'Aguardando Pagamento';
                } else if($value->status == 2){
                    $item_aproved = 'pending';
                    $status = 'Em Análise';
                } else if($value->status == 4){
                    $item_aproved = 'pending';
                    $status = 'Disponível';
                } else if($value->status == 5){
                    $item_aproved = 'pending';
                    $status = 'Em Disputa';
                } else if($value->status == 6){
                    $item_aproved = 'pending';
                    $status = 'Devolvida';
                } else if($value->status == 7){
                    $item_aproved = 'canceled';
                    $status = 'Cancelada';
                }
                
                $row .= '<tr class="'.$item_aproved.'">
                            <td>'.$core->dataTimeBR($value->data).'</td>
                            <td>'.$core->formataMoeda($value->valor).'</td>
                            <td>'.$value->coins.'</td>
                            <td>'.$status.'</td>
                         </tr>';
            }
        } else {
           $row .= '<tr>'
                   . '<td colspan="4" style="test-align: center;">Nenhuma transação efetuada ainda.</td>'
                 . '</tr>'; 
        }
        
        echo $row;
    }
    
    public function setViewTransaction($idUsuario){
        $core = new Core();
        
        $campos = array(
            'visualizado' => '1'
        );
            
        $where = "idUsuario = ".$idUsuario;

        $core->update('transacoes', $campos, $where);
    }
    
    public function getFaturamento(){
        $ano = date('Y');
        $mes = date('m');
        $sql = "SELECT sum(valor) as total FROM transacoes WHERE YEAR(data) = $ano AND MONTH(data) = $mes AND ((status = 3) OR (status = 4))";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacoes = $stmt->fetch();
        
        $taxa_pagseguro = intval($transacoes->total) * (8 / 100);
        
        
        return intval($transacoes->total) - $taxa_pagseguro;
    }
    
    public function getTaxas(){
        $ano = date('Y');
        $mes = date('m');
        
        $sql = "SELECT sum(valor) as total FROM transacoes WHERE YEAR(data) = $ano AND MONTH(data) = $mes AND ((status = 3) OR (status = 4))";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacoes = $stmt->fetch();
        
        $taxa_pagseguro = intval($transacoes->total) * (8 / 100);
        
        
        return $taxa_pagseguro;
    }
    
    public function getFaturamentoMes(){
        $mes = date('m');
        $ano = date('Y');
        
        $sql = "SELECT sum(valor) as total FROM transacoes WHERE status in(3,4) AND MONTH(data) = '$mes' AND YEAR(data) = '$ano'";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacoes = $stmt->fetch();
        
        if(!empty($transacoes)){
            $total = $transacoes->total;
        } else {
            $total = 0;
        }
        
        return $total;
    }
    
    public function getPendentes(){
        $mes = date('m');
        $ano = date('Y');
        
        $sql = "SELECT sum(valor) as total FROM transacoes WHERE YEAR(data) = $ano AND MONTH(data) = $mes AND status = 1";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacoes = $stmt->fetch();
        
        return $transacoes->total;
    }
    
    public function getCancelados(){
        $mes = date('m');
        $ano = date('Y');
        
        $sql = "SELECT sum(valor) as total FROM transacoes WHERE YEAR(data) = $ano AND MONTH(data) = $mes AND status = 7";

        $stmt = DB::prepare($sql);
        $stmt->execute();
        $transacoes = $stmt->fetch();
        
        return $transacoes->total;
    }
}
