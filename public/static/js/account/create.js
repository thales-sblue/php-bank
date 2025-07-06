const { createApp } = Vue;

createApp({
    data() {
        return {
            client: null,
            form: {
                type: ''
            },
            success: '',
            error: ''
        }
    },
    methods: {
        async fetchClientData() {
            try {
                const response = await fetch('/clients/home');
                const data = await response.json();

                if (!response.ok || !data.client) {
                    alert(data.error || 'Erro ao carregar dados');
                    window.location.href = '/clients/login';
                    return;
                }

                this.client = data.client;

                if (data.accounts && data.accounts.length > 2) {
                    window.location.href = '/home';
                }
            } catch (err) {
                alert('Erro ao carregar dados');
                window.location.href = '/clients/login';
            }
        },

        async submitForm() {
            try {
                const response = await fetch('/accounts', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        client_id: this.client.id,
                        type: this.form.type,
                        balance: 0
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = data.message;
                    this.error = '';
                    setTimeout(() => {
                        window.location.replace('/clients/auth');
                    }, 1500);
                } else {
                    this.success = '';
                    this.error = data.details || 'Erro ao abrir conta';
                }
            } catch (err) {
                this.success = '';
                this.error = 'Erro na requisição';
            }
        }
    },
    mounted() {
        this.fetchClientData();
    }
}).mount('#app');