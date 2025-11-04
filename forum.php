<?php switch($acao) {
    default: ?>
    <h2 class="title">Bem vindo ao Fórum DB Heroes</h2>
    
    <?php require_once 'includes/menu-forum.php'; ?>
    
    <h2 class="title">Categorias</h2>
    
    <ul class="menu-categorias">
        <?php $forum->getListCategorias(); ?>
    </ul>
    
    <h2 class="title">Últimas Postagens</h2>
    
    <ul class="ultimos-posts lista-posts">
        <?php $forum->getLatestPosts(); ?>
    </ul>
    
    <?php break; ?>
    
    <?php case 'cat': ?>
        <?php 
            $idCategoria = Url::getURL(2);
            if($idCategoria != ''){
                $dados = $core->getDados('forum_categorias', 'WHERE slug = "'.$idCategoria.'"');
            }
        ?>
    
        <h2 class="title">Bem vindo ao Fórum DB Heroes</h2>
    
        <?php require_once 'includes/menu-forum.php'; ?>
        
        <ul class="menu-categorias">
            <?php $forum->getListCategorias(); ?>
        </ul>

        <h2 class="title">Tópicos da Categoria (<strong><?php echo $dados->categoria; ?></strong>)</h2>
        
        <ul class="menu-categorias">
            <?php $forum->getListSubCategorias($idCategoria); ?>
        </ul>
        
        <h2 class="title">Artigos Encontrados</h2>
        
        <ul class="lista-posts">
            <?php $forum->getPostsByCategory($idCategoria); ?>
        </ul>
    <?php break; ?>
        
    <?php case 'sub': ?>
        <?php 
            $idCategoria = Url::getURL(2);
            $idSubCategoria = Url::getURL(3);
            $dados = $core->getDados('forum_subcategorias', 'WHERE slug = "'.$idSubCategoria.'"');
        ?>
    
        <h2 class="title">Bem vindo ao Fórum DB Heroes</h2>
    
        <?php require_once 'includes/menu-forum.php'; ?>
        
        <ul class="menu-categorias">
            <?php $forum->getListCategorias(); ?>
        </ul>
        
        <h2 class="title">Artigos Encontrados</h2>
        
        <ul class="lista-posts">
            <?php $forum->getPostsBySubCategory($idSubCategoria); ?>
        </ul>
    <?php break; ?>
        
    <?php case 'pendentes': ?>
        <h2 class="title">Bem vindo ao Fórum DB Heroes</h2>
        
        <?php require_once 'includes/menu-forum.php'; ?>

        <h2 class="title">Posts Pendentes de Aprovação</h2>

        <ul class="ultimos-posts lista-posts">
            <?php $forum->getPendingPosts(); ?>
        </ul>
    <?php break; ?>
        
    <?php case 'comentarios-pendentes': ?>
        <h2 class="title">Bem vindo ao Fórum DB Heroes</h2>
        
        <?php require_once 'includes/menu-forum.php'; ?>

        <h2 class="title">Comentários Pendentes de Aprovação</h2>

        <ul class="lista-comentarios">
            <?php $forum->getPendingComentarios(); ?>
        </ul>
    <?php break; ?>
        
    <?php case 'post': ?>
        <?php 
            $idPost = Url::getURL(2);
            $dados = $core->getDados('forum_posts', 'WHERE slug = "'.$idPost.'"');
            $dadosUsuario = $core->getDados('usuarios', 'WHERE id = '.$dados->idUsuario);
            $dadosReputacao = $core->getDados('forum_reputacao', 'WHERE pontos_minimo <= '.$dadosUsuario->forum_reputacao.' ORDER BY pontos_minimo DESC LIMIT 1');
            
            if($dados->aprovado == 0 && $user->perfil != 3){
                header('Location: '.BASE.'forum');
                $core->msg('error', 'Este post não pode ser acessado.');
            }
            
            if(isset($_POST['responder'])){
                $campos = array(
                    'idPost' => $dados->id,
                    'idUsuario' => $user->id,
                    'comentario' => addslashes($_POST['comentario']),
                    'data' => date('Y-m-d H:i:s')
                );

                if($core->insert('forum_comentarios', $campos)){
                    $core->msg('sucesso', 'Comentário enviado para Análise.');
                    header('Location: '.BASE.'forum/post/'.$idPost);
                } else {
                    $core->msg('error', 'Ocorreu um Erro ao enviar resposta.');
                }
            }
        ?>
        
        <?php require_once 'includes/menu-forum.php'; ?>
        
        <div class="postagem-view">
            <div class="section-user">
                <img class="foto-user" src="<?php echo BASE.$dadosUsuario->foto; ?>" alt="<?php echo $dadosUsuario->nome; ?>" />
                <h4 class="nome-user"><i class="fas fa-user"></i> <?php echo $dadosUsuario->nome; ?></h4>
                <?php echo $forum->getNivel($dadosUsuario->forum_reputacao, $dadosUsuario->perfil); ?>
                
                <h3>Infos</h3>
                
                <?php 
                    if($dadosUsuario->perfil == 3){
                        $tipoUser = 'Administrador';
                        $reputacao = 1000;
                    } else {
                        $tipoUser = $dadosReputacao->tipo;
                        $reputacao = $dadosUsuario->forum_reputacao;
                    }
                ?>
                
                <span>Grupo: <?php echo $tipoUser; ?></span>
                <span>Registro em: <?php echo $core->dataBR($dadosUsuario->data_cadastro); ?></span>
                <span>Posts: <?php echo $forum->getCountPosts($dadosUsuario->id); ?></span>
                <span>Reputação: <i class="fas fa-plus-circle"></i> <?php echo $reputacao; ?></span>
            </div>
            <div class="section-post">
                <?php if($user->reputacao > 1 || $user->perfil == 3){ ?>
                    <?php if($dados->aprovado == 0){ ?>
                        <a href="<?php echo BASE; ?>forum/aprovar/<?php echo $dados->id; ?>" class="bt-aprovar">
                            <i class="fas fa-check"></i> Aprovar Post
                        </a>
                    <?php } ?>
                <?php } ?>
                
                <h2 class="title"><?php echo $dados->titulo; ?></h2>

                <p class="descricao">
                    <?php echo $dados->mensagem; ?>
                </p>
                
                <div class="comentarios">
                    <a href="<?php echo BASE; ?>forum/post/<?php echo $idPost; ?>/comentar" id="btnComentar"><i class="far fa-comment-dots"></i> Responder</a>
                    
                    <?php if(Url::getURL(3) != null){ ?>
                        <form id="formComents" class="forms" action="" method="post">
                            <div class="campos block" style="width: 95%;">
                                <label>Resposta: </label>
                                <textarea id="descricao" rows="20" name="comentario"></textarea>
                            </div>

                            <input type="submit" id="resposta" class="bts-form" name="responder" value="Enviar Resposta" />
                        </form>
                    <?php } ?>
                    
                    <h2 class="title"><i class="far fa-comments"></i> Respostas</h2>
                    
                    <ul>
                        <?php $forum->getComentarios($dados->id); ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php break; ?>
        
    <?php case 'aprovar': ?>
        <?php
            $idPost = Url::getURL(2);
            $dados = $core->getDados('forum_posts', 'WHERE id = "'.$idPost.'"');
            $dadosUsuario = $core->getDados('usuarios', 'WHERE id = "'.$dados->idUsuario.'"');
            
            if($user->reputacao > 1 || $user->perfil == 3){
                $campos = array(
                    'aprovado' => 1
                );

                $where = 'id="'.$idPost.'"';

                if($core->update('forum_posts', $campos, $where)){
                    
                    $campos_user = array(
                        'forum_reputacao' => intval($dadosUsuario->forum_reputacao) + 1
                    );

                    $where_user = 'id="'.$dadosUsuario->id.'"';
                    
                    $core->update('usuarios', $campos_user, $where_user);
                    
                    $core->msg('sucesso', 'Post Aprovado.');
                    header('Location: '.BASE.'forum/');
                } else {
                    $core->msg('error', 'Erro na Aprovação.');
                }
            } else {
                $core->msg('error', 'Você não tem permissão para aprovar posts.');
            }
        ?>
    <?php break; ?>
        
    <?php case 'aprovar-comentario': ?>
        <?php
            $idComentario = Url::getURL(2);
            
            if($user->reputacao > 1 || $user->perfil == 3){
                $campos = array(
                    'aprovado' => 1
                );

                $where = 'id="'.$idComentario.'"';

                if($core->update('forum_comentarios', $campos, $where)){
                    $core->msg('sucesso', 'Comentário Aprovado.');
                    header('Location: '.BASE.'forum/');
                } else {
                    $core->msg('error', 'Erro na Aprovação.');
                }
            } else {
                $core->msg('error', 'Você não tem permissão para aprovar posts.');
            }
        ?>
    <?php break; ?>
        
    <?php case 'edit': ?>
        
    <?php break; ?>
        
    <?php case 'add': ?>
        <?php 
            if(isset($_POST['publicar'])){
                
                if(isset($_POST['idSubcategoria'])){
                    $idSubcategoria = addslashes($_POST['idSubcategoria']);
                } else {
                    $idSubcategoria = '';
                }
                
                $campos = array(
                    'titulo' => addslashes($_POST['titulo']),
                    'idUsuario' => $user->id,
                    'idCategoria' => addslashes($_POST['idCategoria']),
                    'idSubcategoria' => $idSubcategoria,
                    'mensagem' => addslashes($_POST['descricao']),
                    'data_postagem' => date('Y-m-d H:i:s'),
                    'slug' => $core->slug(addslashes($_POST['titulo']))
                );

                if($core->insert('forum_posts', $campos)){
                    $core->msg('sucesso', 'Postagem enviada para Análise.');
                    header('Location: '.BASE.'forum/');
                } else {
                    $core->msg('error', 'Ocorreu um Erro ao Publicar.');
                }
            }
        ?>
        
        <h2 class="title">Nova Postagem</h2>
        
        <form id="formPost" class="forms" action="" method="post">
            <div class="campos" style="width: 95%;">
                <label>Título: </label>
                <input type="text" name="titulo" value="" required />
            </div>
            <div class="campos" style="width: 270px;">
                <label>Categoria: </label>
                <select name="idCategoria" id="idCategoria" required>
                    <option value="" disabled selected>Selecione a Categoria...</option>
                    <?php $forum->getOptionsCategorias(); ?>
                </select>
            </div>
            <div class="campos" style="width: 270px;">
                <label>Sub Categoria: </label>
                <select name="idSubcategoria" id="idSubcategoria">
                    <option value="" disabled selected>Selecione a Sub Categoria...</option>
                </select>
            </div>
            <div class="campos block" style="width: 95%;">
                <label>Mensagem: </label>
                <textarea id="mensagem" class="text-editor" rows="20" name="descricao"></textarea>
            </div>

            <input type="submit" id="publicar" class="bts-form" name="publicar" value="Enviar" />
        </form>
    <?php break; ?>
<?php } ?>