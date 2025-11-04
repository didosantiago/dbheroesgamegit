<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Forum
 *
 * @author Felipe Faciroli
 */
class Forum {
    public function getListCategorias(){
        $sql = "SELECT * FROM forum_categorias WHERE status = 1 ORDER BY categoria ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<li class="forum-categoria '.$value->slug.'">
                        <a href="'.BASE.'forum/cat/'.$value->slug.'">
                            '.$value->icon.'
                            '.$value->categoria;
                        $row .= '</a>';
                    $row .= '</li>';
        }
        
        echo $row;
    }
    
    public function getListSubCategorias($idCategoria){        
        $sql = "SELECT * FROM forum_categorias WHERE slug = '$idCategoria'";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $categoria = $stmt->fetch();
        
        $sql = "SELECT * FROM forum_subcategorias WHERE idCategoria = $categoria->id AND status = 1 ORDER BY subcategoria ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $row .= '<li class="forum-subcategoria '.$value->slug.'">
                            <a href="'.BASE.'forum/sub/'.$categoria->slug.'/'.$value->slug.'">
                                '.$value->subcategoria;
                            $row .= '</a>';
                        $row .= '</li>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getOptionsCategorias(){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_categorias";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        foreach ($item as $key => $value) {
            $row .= '<option value="'.$value->id.'" '.$core->isNewSelected($value->id, $value->id).'>'.$value->categoria.'</option>';
        }
        
        echo $row;
    }
    
    public function getOptionsSubCategorias($idCategoria){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_subcategorias WHERE idCategoria = $idCategoria AND status = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
        foreach ($item as $key => $value) {
            $row .= '<option value="'.$value->id.'" '.$core->isNewSelected($value->id, $value->id).'>'.$value->subcategoria.'</option>';
        }
        } else {
            $row .= '<option value="" disabled selected>Nenhuma Sub Categoria encontrada</option>';
        }
        
        echo $row;
    }
    
    public function getLatestPosts(){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_posts WHERE aprovado = 1 AND fechado = 0 ORDER BY id DESC LIMIT 10";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                $dadosCategoria = $core->getDados('forum_categorias', 'WHERE id = '.$value->idCategoria);
                $dadosSubCategoria = $core->getDados('forum_subcategorias', 'WHERE id = '.$value->idSubcategoria);
                
                $row .= '<li class="forum-post">
                            <img src="'.BASE.$dadosUsuario->foto.'" alt="Foto Usuário" />
                            <div class="informacoes">
                                <h3>
                                    '.$value->titulo.'
                                </h3>
                                <p>'.$core->criaResumo($value->mensagem, 150).'</p>
                                <div class="tags">
                                    <span class="categoria '.$dadosCategoria->slug.'">
                                        <a href="'.BASE.'forum/cat/'.$dadosCategoria->slug.'">
                                            '.$dadosCategoria->icon.'
                                            '.$dadosCategoria->categoria.'
                                        </a>
                                    </span>';
                                    if(!empty($dadosSubCategoria)){
                                        $row .= '<span class="sub-categoria">
                                                    <a href="'.BASE.'forum/sub/'.$dadosSubCategoria->slug.'">
                                                        '.$dadosSubCategoria->subcategoria.'
                                                    </a>
                                                 </span>';
                                    }
                                    $row .= '<span class="postado-por">Postado por <strong>'.$dadosUsuario->nome.'</strong></span>
                                    <span class="data">dia <strong>'.$core->dataTimeBR($value->data_postagem).'</strong></span>
                                    <a class="ver-mais" href="'.BASE.'forum/post/'.$value->slug.'">Ver Mais <i class="fas fa-play"></i></a>
                                </div>
                            </div>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getPostsByCategory($category){
        $core = new Core();
        
        $dadosCat = $core->getDados('forum_categorias', 'WHERE slug = "'.$category.'"');
        
        $sql = "SELECT * FROM forum_posts WHERE idCategoria = $dadosCat->id AND aprovado = 1 AND fechado = 0 ORDER BY id DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                $dadosCategoria = $core->getDados('forum_categorias', 'WHERE id = '.$value->idCategoria);
                $dadosSubCategoria = $core->getDados('forum_subcategorias', 'WHERE id = '.$value->idSubcategoria);
                
                $row .= '<li class="forum-post">
                            <img src="'.BASE.$dadosUsuario->foto.'" alt="Foto Usuário" />
                            <div class="informacoes">
                                <h3>
                                    '.$value->titulo.'
                                </h3>
                                <p>'.$core->criaResumo($value->mensagem, 150).'</p>
                                <div class="tags">
                                    <span class="categoria '.$dadosCategoria->slug.'">
                                        <a href="'.BASE.'forum/cat/'.$dadosCategoria->slug.'">
                                            '.$dadosCategoria->icon.'
                                            '.$dadosCategoria->categoria.'
                                        </a>
                                    </span>';
                                    if(!empty($dadosSubCategoria)){
                                        $row .= '<span class="sub-categoria">
                                                    <a href="'.BASE.'forum/sub/'.$dadosSubCategoria->slug.'">
                                                        '.$dadosSubCategoria->subcategoria.'
                                                    </a>
                                                 </span>';
                                    }
                                    $row .= '<span class="postado-por">Postado por <strong>'.$dadosUsuario->nome.'</strong></span>
                                    <span class="data">dia <strong>'.$core->dataTimeBR($value->data_postagem).'</strong></span>
                                    <a class="ver-mais" href="'.BASE.'forum/post/'.$value->slug.'">Ver Mais <i class="fas fa-play"></i></a>
                                </div>
                            </div>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getPostsBySubCategory($subcategory){
        $core = new Core();
        
        $dadosSubCat = $core->getDados('forum_subcategorias', 'WHERE slug = "'.$subcategory.'"');
        
        $sql = "SELECT * FROM forum_posts WHERE idSubcategoria = $dadosSubCat->id AND aprovado = 1 ORDER BY id DESC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                $dadosCategoria = $core->getDados('forum_categorias', 'WHERE id = '.$value->idCategoria);
                $dadosSubCategoria = $core->getDados('forum_subcategorias', 'WHERE id = '.$value->idSubcategoria);
                
                $row .= '<li class="forum-post">
                            <img src="'.BASE.$dadosUsuario->foto.'" alt="Foto Usuário" />
                            <div class="informacoes">
                                <h3>
                                    '.$value->titulo.'
                                </h3>
                                <p>'.$core->criaResumo($value->mensagem, 150).'</p>
                                <div class="tags">
                                    <span class="categoria '.$dadosCategoria->slug.'">
                                        <a href="'.BASE.'forum/cat/'.$dadosCategoria->slug.'">
                                            '.$dadosCategoria->icon.'
                                            '.$dadosCategoria->categoria.'
                                        </a>
                                    </span>';
                                    if(!empty($dadosSubCategoria)){
                                        $row .= '<span class="sub-categoria">
                                                    <a href="'.BASE.'forum/sub/'.$dadosSubCategoria->slug.'">
                                                        '.$dadosSubCategoria->subcategoria.'
                                                    </a>
                                                 </span>';
                                    }
                                    $row .= '<span class="postado-por">Postado por <strong>'.$dadosUsuario->nome.'</strong></span>
                                    <span class="data">dia <strong>'.$core->dataTimeBR($value->data_postagem).'</strong></span>
                                    <a class="ver-mais" href="'.BASE.'forum/post/'.$value->slug.'">Ver Mais <i class="fas fa-play"></i></a>
                                </div>
                            </div>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getPendingPosts(){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_posts WHERE aprovado = 0 AND fechado = 0 ORDER BY id ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                $dadosCategoria = $core->getDados('forum_categorias', 'WHERE id = '.$value->idCategoria);
                $dadosSubCategoria = $core->getDados('forum_subcategorias', 'WHERE id = '.$value->idSubcategoria);
                
                $resumo = $core->criaResumo($value->mensagem, 150);
                
                $row .= '<li class="forum-post">
                            <img src="'.BASE.$dadosUsuario->foto.'" alt="Foto Usuário" />
                            <div class="informacoes">
                                <h3>
                                    '.$value->titulo.'
                                </h3>
                                <p>'.$resumo.'</p>
                                <div class="tags">
                                    <span class="categoria '.$dadosCategoria->slug.'">
                                        <a href="'.BASE.'forum/cat/'.$dadosCategoria->slug.'">
                                            '.$dadosCategoria->icon.'
                                            '.$dadosCategoria->categoria.'
                                        </a>
                                    </span>';
                                    if(!empty($dadosSubCategoria)){
                                        $row .= '<span class="sub-categoria">
                                                    <a href="'.BASE.'forum/sub/'.$dadosSubCategoria->slug.'">
                                                        '.$dadosSubCategoria->subcategoria.'
                                                    </a>
                                                 </span>';
                                    }
                                    $row .= '<span class="postado-por">Postado por <strong>'.$dadosUsuario->nome.'</strong></span>
                                    <span class="data">dia <strong>'.$core->dataTimeBR($value->data_postagem).'</strong></span>
                                    <a class="ver-mais" href="'.BASE.'forum/post/'.$value->slug.'">Ver Mais <i class="fas fa-play"></i></a>
                                </div>
                            </div>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }

    public function getPendingComentarios(){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_comentarios WHERE aprovado = 0 ORDER BY id ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                
                $row .= '<li class="forum-comentario">
                            <img src="'.BASE.$dadosUsuario->foto.'" alt="Foto Usuário" />
                            <div class="informacoes">
                                <p>'.$value->comentario.'</p>
                                <div class="tags">';
                                    $row .= '<span class="postado-por">Respondido por <strong>'.$dadosUsuario->nome.'</strong></span>
                                    <span class="data">dia <strong>'.$core->dataTimeBR($value->data).'</strong></span>
                                    <a class="ver-mais" href="'.BASE.'forum/aprovar-comentario/'.$value->id.'">Aprovar <i class="fa fa-check"></i></a>
                                </div>
                            </div>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhum registro encontrado!</p>
                     </li>';
        }
        
        echo $row;
    }
    
    public function getNivel($pontos, $perfil){
        $sql = "SELECT * FROM forum_reputacao WHERE pontos_minimo <= $pontos ORDER BY pontos_minimo DESC LIMIT 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($perfil != 3){
            $row = '<div class="escudo">
                    <img src="'.BASE.'assets/forum/'.$item->escudo.'" alt="'.$item->tipo.'" />
                </div>';
        } else {
            $row = '<div class="escudo">
                    <img src="'.BASE.'assets/forum/escudo-administrador.png" alt="Administrador" />
                </div>';
        }
        
        return $row;
    }
    
    public function getCountPosts($idUsuario){
        $sql = "SELECT count(*) as total FROM forum_posts WHERE idUsuario = $idUsuario AND aprovado = 1";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch();
        
        if($stmt->rowCount() > 0){
            $total = $item->total;
        } else {
            $total = 0;
        }
        
        return $total;
    }
    
    public function getComentarios($idPost){
        $core = new Core();
        
        $sql = "SELECT * FROM forum_comentarios WHERE idPost = $idPost AND aprovado = 1 ORDER BY id ASC";
        $stmt = DB::prepare($sql);
        $stmt->execute();
        $item = $stmt->fetchAll();
        
        $row = '';
        
        if($stmt->rowCount() > 0){
            foreach ($item as $key => $value) {
                $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$value->idUsuario);
                $dadosReputacao = $core->getDados('forum_reputacao', 'WHERE pontos_minimo <= '.$dadosUsuario->forum_reputacao.' ORDER BY pontos_minimo DESC LIMIT 1');
                
                $row .= '<li class="coments-view">
                            <div class="section-user">
                                <img class="foto-user" src="'.BASE.$dadosUsuario->foto.'" alt="'.$dadosUsuario->nome.'" />
                                <h4 class="nome-user"><i class="fas fa-user"></i> '.$dadosUsuario->nome.'</h4>
                                '.$this->getNivel($dadosUsuario->forum_reputacao, $dadosUsuario->perfil).'

                                <h3>Infos</h3>';

                                if($dadosUsuario->perfil == 3){
                                    $tipoUser = 'Administrador';
                                    $reputacao = 1000;
                                } else {
                                    $tipoUser = $dadosReputacao->tipo;
                                    $reputacao = $dadosUsuario->forum_reputacao;
                                }

                                 $row .= '<span>Grupo: '.$tipoUser.'</span>
                                <span>Registro em: '.$core->dataBR($dadosUsuario->data_cadastro).'</span>
                                <span>Posts: '.$this->getCountPosts($dadosUsuario->id).'</span>
                                <span>Reputação: <i class="fas fa-plus-circle"></i> '.$reputacao.'</span>
                            </div>
                            <div class="section-comentario">
                                <p class="descricao">
                                    '.$value->comentario.'
                                </p>
                            </div>
                        </li>';
            }
        } else {
            $row .= '<li class="nenhum-registro">
                        <p>Nenhuma resposta.</p>
                     </li>';
        }
        
        echo $row;
    }
}
