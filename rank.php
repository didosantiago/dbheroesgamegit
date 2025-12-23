<?php require_once 'front/header-front.php'; ?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
        color: #fff;
        overflow-x: hidden;
        min-height: 100vh;
    }

    .secao-ranking {
        padding: 80px 20px 60px 0px;
        display: flex;
        justify-content: center;
        margin: -80px 0px 0px 0px;
    }

    .secao-ranking .stm-container {
        max-width: 900px;
        width: 100%;
        position: relative;
    }

    .titulo-ranking {
        text-align: center;
        margin-bottom: 25px;
    }

    .titulo-ranking h2 {
        font-size: 26px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .lista-ranking-cards {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .rank-card {
        display: grid;
        grid-template-columns: 70px 70px 1fr 90px 120px;
        align-items: center;
        padding: 12px 18px;
        border-radius: 18px;
        background: radial-gradient(circle at top left, rgba(34, 197, 94, 0.18), rgba(6, 78, 59, 0.96));
        border: 1px solid rgba(16, 185, 129, 0.7);
        box-shadow: 0 22px 40px rgba(0, 0, 0, 0.9);
        position: relative;
        overflow: hidden;
        transform: translateY(15px);
        opacity: 0;
        animation: cardIn 0.45s ease forwards;
    }

    .rank-card:nth-child(1) { animation-delay: .05s; }
    .rank-card:nth-child(2) { animation-delay: .10s; }
    .rank-card:nth-child(3) { animation-delay: .15s; }
    .rank-card:nth-child(4) { animation-delay: .20s; }
    .rank-card:nth-child(5) { animation-delay: .25s; }
    .rank-card:nth-child(6) { animation-delay: .30s; }
    .rank-card:nth-child(7) { animation-delay: .35s; }
    .rank-card:nth-child(8) { animation-delay: .40s; }
    .rank-card:nth-child(9) { animation-delay: .45s; }
    .rank-card:nth-child(10){ animation-delay: .50s; }

    @keyframes cardIn {
        to { transform: translateY(0); opacity: 1; }
    }

    .rank-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(115deg,
            rgba(16, 185, 129, 0.25),
            transparent 40%,
            rgba(74, 222, 128, 0.25)
        );
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .rank-card:hover::before {
        opacity: 1;
    }

    .rank-card:hover {
        transform: translateY(-4px) scale(1.015);
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.85);
    }

    .rank-pos {
        text-align: center;
    }

    /* medal base */
    .rank-pos span {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        height: 50px;
        padding: 0;
        border-radius: 50%;
        background: radial-gradient(circle, #22c55e, #4ade80);
        color: #022c22;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 0 16px rgba(16, 185, 129, 0.9);
    }

    /* fita da medalha */
    .rank-pos span::before {
        content: '';
        position: absolute;
        top: -10px;
        width: 18px;
        height: 20px;
        background: linear-gradient(180deg, #1d4ed8 0%, #ef4444 100%);
        border-radius: 4px 4px 0 0;
    }

    /* ouro */
    .rank-pos-1 span {
        background: radial-gradient(circle, #fef9c3, #fbbf24);
        color: #78350f;
        box-shadow: 0 0 20px rgba(250, 204, 21, 0.9);
    }

    /* prata */
    .rank-pos-2 span {
        background: radial-gradient(circle, #e5e7eb, #9ca3af);
        color: #111827;
        box-shadow: 0 0 20px rgba(148, 163, 184, 0.9);
    }

    /* bronze */
    .rank-pos-3 span {
        background: radial-gradient(circle, #fed7aa, #f97316);
        color: #7c2d12;
        box-shadow: 0 0 20px rgba(248, 153, 72, 0.9);
    }

    /* posições sem medalha/fita */
    .rank-pos-4 span::before,
    .rank-pos-5 span::before,
    .rank-pos-6 span::before,
    .rank-pos-7 span::before,
    .rank-pos-8 span::before,
    .rank-pos-9 span::before,
    .rank-pos-10 span::before {
        content: none;
    }

    .rank-avatar img {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        object-fit: cover;
        border: 2px solid rgba(74, 222, 128, 0.9);
        box-shadow: 0 0 20px rgba(22, 163, 74, 1);
    }

    /* área central: nome + graduação */
    .rank-main {
        padding: 0 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    /* nome do jogador fixo à esquerda */
    .rank-name {
        min-width: 160px;         /* trava a coluna do nome */
    }
    .rank-name strong {
        color: #ffc9e9;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.9);
        font-size: 16px;
    }

    /* bloco graduação: fica no centro entre nome e nível */
    .rank-grad {
        display: flex;
        align-items: center;
        justify-content: center;   /* centro da faixa central */
        gap: 8px;
        flex: 1;                   /* ocupa todo o espaço entre nome e nível */
    }

    /* texto da graduação: largura fixa + alinhado à esquerda */
    .rank-grad span {
        display: inline-block;
        width: 180px;              /* ajuste esse valor até ficar perfeito */
        text-align: left;          /* início igual em todas as linhas */
        font-size: 13px;
        color: #e4ff00;
    }

    /* ícone ao lado do texto */
    .rank-grad-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: contain;
        filter: drop-shadow(0 0 6px rgba(0,0,0,0.8));
    }


    .rank-level {
        text-align: center;
    }

    .rank-level span {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: #e4ff00;
    }

    .rank-level strong {
        display: block;
        font-size: 18px;
        color: #bbf7d0;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.9);
    }

    .rank-planet {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .rank-planet img {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        object-fit: cover;
        box-shadow: 0 0 18px rgba(0, 0, 0, 0.9);
    }

    .rank-planet span {
        font-size: 13px;
        color: #dcfce7;
    }

    @media (max-width: 768px) {
        .rank-card {
            grid-template-columns: 60px 60px 1fr;
            grid-template-rows: auto auto;
        }
        .rank-level,
        .rank-planet {
            margin-top: 8px;
            justify-content: flex-start;
        }
    }
</style>
<div class="rank-banner"></div>
<div class="bg-overlay"></div>   <!-- overlay em todas as páginas -->
<div class="secao-ranking">
    <div class="stm-container">
        <div class="titulo-ranking">
            <h2>Ranking TOP 10 Jogadores</h2>
        </div>

        <div class="lista-ranking-cards">
            <?php
            $ranking = $personagem->getRankingArray();
            $pos = 1;
            foreach ($ranking as $r):
            ?>
            <div class="rank-card <?php echo $pos <= 3 ? 'top3' : ''; ?>">
                <div class="rank-pos rank-pos-<?php echo $pos; ?>">
                    <span>
                        <?php
                        if ($pos === 1) {
                            echo '1º';
                        } elseif ($pos === 2) {
                            echo '2º';
                        } elseif ($pos === 3) {
                            echo '3º';
                        } else {
                            echo $pos . 'º';
                        }
                        ?>
                    </span>
                </div>

                <div class="rank-avatar">
                    <img src="<?php echo $r['foto']; ?>" alt="<?php echo htmlspecialchars($r['guerreiro']); ?>">
                </div>

                <div class="rank-main">
                    <div class="rank-name">
                        <strong><?php echo htmlspecialchars($r['guerreiro']); ?></strong>
                    </div>

                    <div class="rank-grad">
                        <span><?php echo htmlspecialchars($r['graduacao']); ?></span>
                        <img class="rank-grad-icon"
                             src="<?php echo $r['graduacao_icone']; ?>"
                             alt="<?php echo htmlspecialchars($r['graduacao']); ?>">
                    </div>
                </div>

                <div class="rank-level">
                    <span>Nível</span>
                    <strong><?php echo (int)$r['level']; ?></strong>
                </div>

                <div class="rank-planet">
                    <span><?php echo htmlspecialchars($r['planeta']); ?></span>
                    <img src="<?php echo $r['planeta_img']; ?>" alt="<?php echo htmlspecialchars($r['planeta']); ?>">
                    
                </div>
            </div>
            <?php
                $pos++;
            endforeach;
            ?>
        </div>
    </div>
</div>

<?php require_once 'front/footer-front.php'; ?>
