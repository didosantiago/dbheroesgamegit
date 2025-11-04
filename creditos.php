<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">Adquirir Créditos</h2>

<div class="detalhes-vip">
    <p>O DB HEREOS RPG é um jogo gratuito e sem fins lucrativos, e por isso a contribuição de seus jogadores é fundamental para que, cada dia mais, o jogo se desenvolva e melhore suas funcionalidades. Qualquer tipo de arrecadação ou doações feitas ao DB Heroes serão revertidas em manutenção e melhorias ao site, bem como divulgação deste e do anime. E contribuindo com sua doação ao jogo, além de nos ajudar a cada dia melhorar o DB Heroes, você, jogador, passa a ser um Jogador VIP, com acesso à vantagens exclusivas.</p>
    <br>
    <p>- Seus créditos não expiram por falta de uso.</p>
    <p>- Todos os personagens de sua conta podem usufluir dos créditos.</p>
    <p>- Estar colaborando com a manuntenção e evolução do jogo.</p>
</div>

<ul class="botoes-doacao">
    <li>
        <div class="img"></div>
        <h3>Nome</h3>
        <span>Créditos</span>
        <span class="coins">Valor</span>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 5 Coins</span>
        <span class="coins">R$ 5,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar5" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar5" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor5" checked value="5" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 10 Coins</span>
        <span class="coins">R$ 10,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar10" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar10" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor10" checked value="10" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 15 Coins</span>
        <span class="coins">R$ 15,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar15" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar15" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor15" checked value="15" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 20 Coins</span>
        <span class="coins">R$ 20,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar20" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar20" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor20" checked value="20" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 25 Coins</span>
        <span class="coins">R$ 25,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar25" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar25" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor25" checked value="25" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 30 Coins</span>
        <span class="coins">R$ 30,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar30" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar30" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="30" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 35 Coins</span>
        <span class="coins">R$ 35,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar35" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar35" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="35" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 40 Coins</span>
        <span class="coins">R$ 40,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar40" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar40" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="40" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 45 Coins</span>
        <span class="coins">R$ 45,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar45" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar45" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="45" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 50 Coins</span>
        <span class="coins">R$ 50,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar50" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar50" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="50" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 100 Coins</span>
        <span class="coins">R$ 100,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar100" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar100" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="100" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img class="img-coin" src="<?php echo BASE.'assets/icones/coin.png' ?>" />
        <h3>Coins (MOEDA DO JOGO)</h3>
        <span class="creditos">Ganha 500 Coins</span>
        <span class="coins">R$ 500,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar500" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar500" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="500" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    
    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
        <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
    <?php } else { ?>
        <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
    <?php } ?>
</ul>