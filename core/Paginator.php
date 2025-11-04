<?php
    class Paginator {	
        public $total_reg = 20;
        public $num_paginas;
        public $max_links = 5;


        public function inicio($pc, $counter, $total_reg){
            $inicio = $pc - 1;
            $inicio = $inicio * $total_reg;
            
            $this->num_paginas = ceil($counter / $total_reg);
            
            return $inicio;
        }
        
        public function paginar($pc, $tp){
            // agora vamos criar os botões "Anterior e próximo"
            $anterior = $pc - 1;
            $proximo = $pc + 1;
            
            $links_laterais = ceil($this->max_links / 2);
            $qtd = $pc - $links_laterais;
            $limitar = $pc + $links_laterais;
            
            $box_paginator = '<ul class="paginator">';
            
            $box_paginator .= " ";
            
            $box_paginator .= "<li class='page-first'><a href='?pagina=1'>Primeira</a></li>";
            
            if ($pc > 1) {
                $box_paginator .= "<li class='page-prev'><a href='?pagina=$anterior'>Anterior</a></li>";
            }
            
            for($i = $qtd; $i <= $limitar + 1; $i++) { 
                if($i == $pc){
                    $box_paginator .= "<li class='page current'><a class='page current' href='?pagina=$i'>".$i."</a></li>"; 
                } else {
                    if ($i >= 1 && $i <= $this->num_paginas){
                        $box_paginator .= "<li class='page'><a href='?pagina=$i'>".$i."</a></li>"; 
                    }
                }
            } 
            
            if ($pc < $tp) {
                $box_paginator .= "<li class='page-next'><a href='?pagina=$proximo'>Próxima</a></li>";
            }

            $box_paginator .= "<li class='page-last'><a href='?pagina=$this->num_paginas'>Última</a></li>";
            
            $box_paginator .= "</ul>";
            
            return $box_paginator;
        }
    }
?>