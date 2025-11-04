<?php 
    if(!isset($_SESSION['PERSONAGEMID'])){
        header('Location: '.BASE.'portal');
    }
?>

<h2 class="title">Seja VIP</h2>

<div class="detalhes-vip">
    <p>O DB HEREOS RPG é um jogo gratuito e sem fins lucrativos, e por isso a contribuição de seus jogadores é fundamental para que, cada dia mais, o jogo se desenvolva e melhore suas funcionalidades. Qualquer tipo de arrecadação ou doações feitas ao DB Heroes serão revertidas em manutenção e melhorias ao site, bem como divulgação deste e do anime. E contribuindo com sua doação ao jogo, além de nos ajudar a cada dia melhorar o DB Heroes, você, jogador, passa a ser um Jogador VIP, com acesso à vantagens exclusivas.</p>
    <br>
    <p>Ao adquirir seu Plano Vip tenha a certeza que:</p>
    <p>- Seus créditos não expiram por falta de uso.</p>
    <p>- A sua conta será Vip por tempo indeterminado e participará sempre de promoções exclusivas ( Vantagens Vip ).</p>
    <p>- Todos os personagens de sua conta podem usufluir dos créditos.</p>
    <p>- Estar colaborando com a manuntenção e evolução do jogo.</p>
    <br>
    <p>Com a opção PagSeguro, você poderá doar com todos os Cartões de Crédito, Transferências Bancárias e Boletos Bancários. 
    Abaixo segue a lista de planos do sistema VIP do DB Heroes:</p>
    <img src="<?php echo BASE.'assets/pagseguro.png' ?>" />
    <br>
    <p>O Sistema VIP permite usufluir do valor doado de forma inteligente, e o valor doado é convertido para você em créditos, conforme descrito abaixo:</p>
</div>

<ul class="botoes-doacao">
    <li>
        <div class="img"></div>
        <h3>Nome</h3>
        <span>Créditos</span>
        <span class="coins">Valor</span>
    </li>
    <li class="even">
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Son Gohan</h3>
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
    <li>
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Kame</h3>
        <span class="creditos">Ganha 20 Coins</span>
        <span class="coins">R$ 20,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar40" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar40" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" id="valor20" checked value="20" />
            <input type="hidden" name="idUsuario" id="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Karin</h3>
        <span class="creditos">Ganha 30 Coins</span>
        <span class="coins">R$ 30,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar50" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar50" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="30" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li>
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Sr. Kaio</h3>
        <span class="creditos">Ganha 40 Coins</span>
        <span class="coins">R$ 40,00</span>
        
        <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
            <form id="formDoar100" class="forms" action="https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } else { ?>
            <form id="formDoar100" class="forms" action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="DBH.pagamentos.pagamentoDoacao(this); return false;">
        <?php } ?>

            <input type="hidden" name="code" id="code" value="" />
            <input type="hidden" name="idPersonagem" value="<?php echo $_SESSION['PERSONAGEMID'] ?>" />
            <input type="hidden" name="valor" checked value="40" />
            <input type="hidden" name="idUsuario" checked value="<?php echo $user->id; ?>" />

            <button type="submit" class="bt-doar" name="doar">Finalizar Doação</button>
        </form>
    </li>
    <li class="even">
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Bils</h3>
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
    <li>
        <img src="<?php echo BASE.'assets/ico-vip.png' ?>" />
        <h3>VIP Whis</h3>
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
    
    <?php if($config->PAGSEGURO_ENV == 'sandbox'){ ?>
        <script type="text/javascript" src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
    <?php } else { ?>
        <script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
    <?php } ?>
</ul>