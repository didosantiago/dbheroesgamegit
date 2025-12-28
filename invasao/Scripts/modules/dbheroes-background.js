// ========== DBHEROES BACKGROUND ANIMATION ==========
// Inicializar background animado automaticamente

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se já existe background (evitar duplicação)
    if(document.querySelector('.digital-bg')) {
        return;
    }
    
    // Criar elementos de background
    const digitalBg = document.createElement('div');
    digitalBg.className = 'digital-bg';
    document.body.appendChild(digitalBg);
    
    const hudGrid = document.createElement('div');
    hudGrid.className = 'hud-grid';
    document.body.appendChild(hudGrid);
    
    // Criar partículas de dados
    const dataSymbols = ['0', '1', '01', '10', '001', '101', '▲', '►', '◆'];
    for (let i = 0; i < 30; i++) {
        const particle = document.createElement('div');
        particle.className = 'data-particle';
        particle.textContent = dataSymbols[Math.floor(Math.random() * dataSymbols.length)];
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 10 + 's';
        particle.style.animationDuration = (Math.random() * 5 + 8) + 's';
        document.body.appendChild(particle);
    }
    
    // Criar orbes brilhantes
    for (let i = 0; i < 5; i++) {
        const orb = document.createElement('div');
        orb.className = 'glowing-orb';
        orb.style.left = (Math.random() * 80 + 10) + '%';
        orb.style.top = (Math.random() * 80 + 10) + '%';
        orb.style.animationDelay = Math.random() * 4 + 's';
        document.body.appendChild(orb);
    }
});
