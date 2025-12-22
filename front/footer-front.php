    <!-- ===== FOOTER ===== -->
    <footer class="footer-front">
        <div class="footer-content">
            <!-- Social Media Section -->
            <div class="footer-social">
                <div class="social-section">
                    <h3>INSTAGRAM</h3>
                    <p>Acompanhe tudo o que rola no nosso dia a dia :)</p>
                    <a href="https://www.instagram.com/dbheroes4life" target="_blank" class="instagram-btn">
                        <i class="fab fa-instagram"></i>
                        IR PARA O INSTAGRAM
                    </a>
                </div>
                
                <div class="social-section">
                    <h3>FACEBOOK</h3>
                    <p>Acompanhe nossa página</p>
                    <a href="https://www.facebook.com/dbheroesgame" target="_blank" class="facebook-btn">
                        <i class="fab fa-facebook-f"></i>
                        CURTIR PÁGINA
                    </a>
                </div>
            </div>
            
            <!-- Footer Links -->
            <div class="footer-links">
                <a href="<?php echo BASE; ?>doc/aviso-legal.pdf" target="_blank">Aviso Legal</a>
                <a href="<?php echo BASE; ?>doc/politica-de-privacidade.pdf" target="_blank">Política de Privacidade</a>
                <a href="<?php echo BASE; ?>doc/termos-de-uso.pdf" target="_blank">Termos de Uso</a>
                <a href="<?php echo BASE; ?>doc/regras.pdf" target="_blank">Regras & Punições</a>
            </div>
            
            <!-- Social Icons -->
            <div class="footer-social-icons">
                <a href="https://discord.gg/f4BmxKNr" target="_blank" title="Discord">
                    <i class="fab fa-discord"></i>
                </a>
                <a href="https://www.facebook.com/dbheroesgame" target="_blank" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/dbheroes4life" target="_blank" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.youtube.com/channel/UCMBvnTxxA5YFGQLWc2ACtcg" target="_blank" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>
                    <i class="fas fa-shield-alt shield-icon"></i>
                    Seus dados estão protegidos e criptografados
                </p>
                <p>© 2019 DB Heroes Game RPG - SINCE 2018</p>
                <p style="font-size: 11px; color: #555; margin-top: 10px;">
                    Personagens e desenhos © CopyRight 1984 by Akira Toriyama. Todos os direitos reservados
                </p>
            </div>
        </div>
    </footer>
    
    <style>
        /* ===== FOOTER STYLING ===== */
        .footer-front {
            background: rgba(0, 0, 0, 0.95);
            padding: 50px 20px 30px;
            margin-top: 80px;
            border-top: 2px solid rgba(255, 193, 7, 0.2);
            font-family: 'Poppins', sans-serif;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-social {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .social-section {
            text-align: center;
            flex: 1;
            min-width: 280px;
            max-width: 400px;
        }
        
        .social-section h3 {
            color: #ffc107;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .social-section p {
            color: #999;
            font-size: 14px;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .social-section a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .instagram-btn {
            background: linear-gradient(135deg, #833ab4 0%, #fd1d1d 50%, #fcb045 100%);
            color: white;
        }
        
        .instagram-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(131, 58, 180, 0.5);
        }
        
        .facebook-btn {
            background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
            color: white;
        }
        
        .facebook-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(59, 89, 152, 0.5);
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #ffc107;
        }
        
        .footer-bottom {
            text-align: center;
            color: #666;
            font-size: 13px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .footer-bottom p {
            margin: 8px 0;
        }
        
        .footer-bottom .shield-icon {
            color: #4caf50;
            margin-right: 5px;
        }
        
        .footer-social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .footer-social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .footer-social-icons a:hover {
            background: #ffc107;
            color: #000;
            transform: translateY(-5px);
        }
        
        @media (max-width: 768px) {
            .footer-social {
                gap: 30px;
            }
            
            .social-section {
                min-width: 100%;
            }
        }
    </style>
</body>
</html>
