$(document).ready(function(){
    var $body = $('body');

    DBH.header.init();
    
    if(!$body.hasClass('login')){
        DBH.modal.iniciaModal();
    }

    if($body.hasClass('minhas-fotos')){
    	DBH.personagem.foto();
    }
    
    if($body.hasClass('criar-personagem')){
    	DBH.personagem.init();
    }
    
    if($body.hasClass('troca-guerreiro')){
    	DBH.personagem.trocaGuerreiro();
    }
    
    if($body.hasClass('meus-personagens')){
    	DBH.personagem.meuPersonagem();
    }
    
    if($body.hasClass('inventario')){
    	DBH.inventario.init();
    }
    
    if($body.hasClass('equipes')){
    	DBH.equipes.init();
    }
    
    if($body.hasClass('produto')){
    	DBH.produto.init();
    }
    
    if($body.hasClass('combate')){
    	DBH.pvp.init();
    }
    
    if($body.hasClass('npc')){
    	DBH.npc.init();
    }
    
    if($body.hasClass('forum')){
    	DBH.forum.init();
    }
    
    if($body.hasClass('login')){
    	DBH.login.init();
    }
    
    if($body.hasClass('noticias')){
    	DBH.noticias.init();
    }
    
    if($body.hasClass('equipes')){
    	DBH.chat.equipes();
    }
    
    if($body.hasClass('publico')){
    	DBH.chat.amigos();
    }
    
    if($body.hasClass('home')){
    	DBH.home.init();
    }
    
    if($body.hasClass('assistir')){
    	DBH.assistir.init();
    }
    
    if($body.hasClass('market')){
    	DBH.mercado.init();
    }
    
    if($body.hasClass('invasao')){
    	DBH.invasao.init();
    }
    
    DBH.header.audioTema();
    DBH.header.playTema();
});

$(document).ajaxStop(function() {
    var $body = $('body');
    
    if($body.hasClass('meus-personagens')){
    	DBH.personagem.jogar();
    }
});