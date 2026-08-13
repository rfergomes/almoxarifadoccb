// Registro e Gerenciamento do Service Worker & PWA Install Prompt - Almoxarifado CCB

let deferredPrompt;

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then((registration) => {
        console.log('[PWA] Service Worker registrado com sucesso:', registration.scope);
      })
      .catch((error) => {
        console.error('[PWA] Falha ao registrar o Service Worker:', error);
      });
  });
}

// Captura do evento de instalação do PWA
window.addEventListener('beforeinstallprompt', (e) => {
  // Previne a exibição automática do banner nativo do navegador
  e.preventDefault();
  deferredPrompt = e;
  
  console.log('[PWA] Aplicativo pronto para instalação.');
  
  // Se existir um elemento/botão customizado de instalação na interface
  const installBtn = document.getElementById('pwa-install-btn');
  if (installBtn) {
    installBtn.style.display = 'block';
    installBtn.addEventListener('click', () => {
      installBtn.style.display = 'none';
      deferredPrompt.prompt();
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('[PWA] Usuário aceitou a instalação do App CCB.');
        } else {
          console.log('[PWA] Usuário recusou a instalação.');
        }
        deferredPrompt = null;
      });
    });
  }
});

// Evento quando o aplicativo é instalado com sucesso
window.addEventListener('appinstalled', () => {
  console.log('[PWA] Aplicativo Almoxarifado CCB instalado com sucesso!');
  deferredPrompt = null;
});
