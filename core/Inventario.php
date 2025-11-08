<?php

/* ... (rest of your file remains unchanged above this line) ... */

class Inventario {
    // ... existing methods ...

    /**
     * Initializes 8 equipped slots (3 emblems, 5 normal) for a new character
     */
    public function inicializaSlotsEquipados($idPersonagem) {
        $core = new Core();
        $sql = "SELECT * FROM personagens_itens_equipados WHERE idPersonagem = $idPersonagem AND slot IN (1,2,3,4,5,6,7,8)";
        $stmt = DB::prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() == 0){
            for ($i = 1; $i <= 8; $i++) {
                $emblema = ($i <= 3) ? 1 : 0;
                $campos = array(
                    'idPersonagem' => $idPersonagem,
                    'slot' => $i,
                    'emblema' => $emblema,
                    'adesivo' => 0,
                    'vazio' => 1
                );
                $core->insert('personagens_itens_equipados', $campos);
            }
        }
    }

    // ... rest of your Inventario class
}

/* ... (rest of your file remains unchanged after this line) ... */
