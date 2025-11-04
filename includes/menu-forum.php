<ul class="menu-forum">
    <li>
        <a href="<?php echo BASE; ?>forum">
            <i class="fas fa-home"></i> Ínicio
        </a>
    </li>
    <li class="adicionar">
        <a href="<?php echo BASE; ?>forum/add">
            <i class="fas fa-plus"></i> Fazer Postagem
        </a>
    </li>
    <li class="adicionar">
        <a href="<?php echo BASE; ?>perfil">
            <i class="fas fa-edit"></i> Editar Perfil
        </a>
    </li>
    
    <?php if($user->reputacao > 1 || $user->perfil == 3){ ?>
        <li>
            <a href="<?php echo BASE; ?>forum/pendentes">
                <i class="fas fa-check"></i> Aprovar Posts
            </a>
        </li>
        <li>
            <a href="<?php echo BASE; ?>forum/comentarios-pendentes">
                <i class="fas fa-check"></i> Aprovar Comentários
            </a>
        </li>
    <?php } ?>
</ul>