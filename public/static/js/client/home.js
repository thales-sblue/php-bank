const { createApp } = Vue;

createApp({
    data() {
        return {
            client: null,
            accounts: []
        };
    },
    methods: {
        async fetchClientData() {
            try {
                const response = await fetch('/clients/home');
                const data = await response.json();

                if (!response.ok || !data.client) {
                    alert(data.error || 'Erro ao carregar dados');
                    window.location.replace('/clients/login');
                    return;
                }

                this.client = data.client;
                this.accounts = data.accounts || [];
            } catch (err) {
                alert('Erro ao carregar dados');
                window.location.replace('/clients/login');
            }
        }
    },
    mounted() {
        this.fetchClientData();
    }
}).mount('#app');