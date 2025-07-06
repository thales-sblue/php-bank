const { createApp } = Vue;

createApp({
    data() {
        return {
            client: null,
            accounts: [],
            form: {
                accountId: '',
                type: '',
                amount: ''
            },
            success: '',
            error: ''
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
        },

        selectAccount(id) {
            this.form.accountId = this.form.accountId === id ? '' : id;
        },

        async submitTransaction() {
            this.success = '';
            this.error = '';

            try {
                const response = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accountId: this.form.accountId,
                        type: this.form.type,
                        amount: parseFloat(this.form.amount)
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = data.message || 'Transação realizada com sucesso!';
                    this.form = { accountId: '', type: '', amount: '' };
                    this.fetchClientData();
                } else {
                    this.error = data.details || data.error || 'Erro na transação';
                }
            } catch (err) {
                this.error = 'Erro na requisição';
            }
        }
    },
    mounted() {
        this.fetchClientData();
    }
}).mount('#app');