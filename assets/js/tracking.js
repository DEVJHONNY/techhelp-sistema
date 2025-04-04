class StatusTracking {
    constructor() {
        this.interval = null;
    }

    startTracking(orcamentoId) {
        this.interval = setInterval(() => {
            this.checkStatus(orcamentoId);
        }, 30000); // Verifica a cada 30 segundos
    }

    async checkStatus(orcamentoId) {
        const response = await fetch(`/sistema/api/tracking.php?orcamento=${orcamentoId}`);
        const data = await response.json();
        this.updateInterface(data.tracking);
    }
}
