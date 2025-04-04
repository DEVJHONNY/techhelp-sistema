class NotificationSystem {
    constructor() {
        this.checkPermission();
    }

    async checkPermission() {
        if (!('Notification' in window)) {
            console.log('Navegador não suporta notificações');
            return;
        }

        if (Notification.permission !== 'granted') {
            await Notification.requestPermission();
        }
    }

    async send(title, options = {}) {
        if (Notification.permission === 'granted') {
            const notification = new Notification(title, {
                icon: '/sistema/assets/images/icon.png',
                badge: '/sistema/assets/images/badge.png',
                ...options
            });

            notification.onclick = () => {
                window.focus();
                notification.close();
            };
        }
    }

    async checkUpdates() {
        try {
            const response = await fetch('/sistema/api/check-updates.php');
            const data = await response.json();
            
            if (data.hasUpdates) {
                this.send('TechHelp', {
                    body: 'Você tem atualizações no seu orçamento!',
                    tag: 'update-notification'
                });
            }
        } catch (error) {
            console.error('Erro ao verificar atualizações:', error);
        }
    }
}
