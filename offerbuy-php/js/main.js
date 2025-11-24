    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== SISTEMA INICIADO ===');

        // Elementos
        const overlay = document.getElementById('overlay');
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        const openPopupBtn = document.getElementById('openPopupBtn');
        const popup = document.getElementById('popup');
        const closePopupBtn = document.getElementById('closePopupBtn');
        const loginToggle = document.getElementById('loginToggle');
        const signupToggle = document.getElementById('signupToggle');
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        // Inicializar forms
        if (loginForm && signupForm) {
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
        }

        // Menu Mobile
        if (menuIcon && navbar) {
            menuIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                navbar.classList.toggle('open');
                menuIcon.classList.toggle('bx-x');
                if (overlay) overlay.style.display = navbar.classList.contains('open') ? 'block' : 'none';
                document.body.style.overflow = navbar.classList.contains('open') ? 'hidden' : 'auto';
                
                // Fechar outros menus
                if (userMenu) userMenu.style.display = 'none';
            });

            // Fechar menu ao clicar em links
            document.querySelectorAll('.navbar a').forEach(link => {
                link.addEventListener('click', function() {
                    navbar.classList.remove('open');
                    menuIcon.classList.remove('bx-x');
                    if (overlay) overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            });
        }

        // Popup Login/Cadastro
        if (openPopupBtn && popup) {
            openPopupBtn.addEventListener('click', function() {
                popup.style.display = "flex";
                if (overlay) overlay.style.display = "block";
                document.body.style.overflow = 'hidden';
                
                // Reset para login ao abrir
                if (loginToggle && signupToggle && loginForm && signupForm) {
                    loginToggle.classList.add('active');
                    signupToggle.classList.remove('active');
                    loginForm.style.display = 'block';
                    signupForm.style.display = 'none';
                }
            });
        }

        if (closePopupBtn && popup) {
            closePopupBtn.addEventListener('click', function() {
                popup.style.display = "none";
                if (overlay) overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        // Toggle Login/Cadastro - CORRIGIDO
        if (loginToggle && signupToggle && loginForm && signupForm) {
            loginToggle.addEventListener('click', function() {
                loginToggle.classList.add('active');
                signupToggle.classList.remove('active');
                loginForm.style.display = 'block';
                signupForm.style.display = 'none';
                // Reset forms
                loginForm.reset();
                signupForm.reset();
            });

            signupToggle.addEventListener('click', function() {
                signupToggle.classList.add('active');
                loginToggle.classList.remove('active');
                loginForm.style.display = 'none';
                signupForm.style.display = 'block';
                // Reset forms
                loginForm.reset();
                signupForm.reset();
            });
        }

        // Menu do Usuário
        if (userMenuBtn && userMenu) {
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
                
                // Fechar outros menus
                if (navbar) navbar.classList.remove('open');
                if (menuIcon) menuIcon.classList.remove('bx-x');
            });

            // Fechar menu ao clicar fora
            document.addEventListener('click', function(e) {
                if (userMenu && !userMenu.contains(e.target) && e.target !== userMenuBtn) {
                    userMenu.style.display = 'none';
                }
            });
        }

        // Overlay - CORRIGIDO
        if (overlay) {
            overlay.addEventListener('click', function() {
                // Fechar todos os menus
                if (navbar) {
                    navbar.classList.remove('open');
                    if (menuIcon) menuIcon.classList.remove('bx-x');
                }
                if (popup) popup.style.display = 'none';
                if (userMenu) userMenu.style.display = 'none';
                overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Reset forms ao fechar
                if (loginForm) loginForm.reset();
                if (signupForm) signupForm.reset();
                
                // Voltar para login
                if (loginToggle && signupToggle) {
                    loginToggle.classList.add('active');
                    signupToggle.classList.remove('active');
                    if (loginForm) loginForm.style.display = 'block';
                    if (signupForm) signupForm.style.display = 'none';
                }
            });
        }

        // Tecla ESC - CORRIGIDO
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (popup && popup.style.display === 'flex') {
                    popup.style.display = 'none';
                    if (overlay) overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    
                    // Reset para login
                    if (loginToggle && signupToggle) {
                        loginToggle.classList.add('active');
                        signupToggle.classList.remove('active');
                        if (loginForm) loginForm.style.display = 'block';
                        if (signupForm) signupForm.style.display = 'none';
                    }
                }

                if (navbar && navbar.classList.contains('open')) {
                    navbar.classList.remove('open');
                    if (menuIcon) menuIcon.classList.remove('bx-x');
                    if (overlay) overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }

                if (userMenu) {
                    userMenu.style.display = 'none';
                }
            }
        });

        // Redimensionamento
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1170) {
                if (navbar) navbar.classList.remove('open');
                if (menuIcon) menuIcon.classList.remove('bx-x');
                if (overlay) overlay.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Prevenir envio duplo de formulários
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    setTimeout(() => {
                        submitBtn.disabled = false;
                    }, 3000);
                }
            });
        });

        console.log('=== SISTEMA CONFIGURADO ===');
    }); 