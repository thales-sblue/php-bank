const { createApp } = Vue;

createApp({
    data() {
        return {
            client: null,
            accounts: [],
            destinationAccounts: [],
            users: [],
            selectedUserId: '',
            form: {
                fromAccountId: '',
                toAccountId: '',
                amount: ''
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
                    window.location.replace('/clients/login');
                    return;
                }
                this.client = data.client;
                this.accounts = data.accounts || [];
            } catch {
                this.error = 'Erro ao carregar seus dados';
            }
        },

        async fetchAllUsers() {
            try {
                const response = await fetch('/clients');
                const data = await response.json();
                this.users = data;
            } catch {
                this.error = 'Erro ao carregar lista de usuários';
            }
        },

        async fetchUserAccounts() {
            try {
                this.error = '';
                this.destinationAccounts = '';
                this.form.toAccountId = '';
                const response = await fetch(`/accounts/${this.selectedUserId}`);
                const data = await response.json();

                if (response.ok) {
                    this.destinationAccounts = data;
                } else {
                    this.error = data.details || data.error;
                }
            } catch {
                this.error = 'Erro ao carregar contas do usuário';
            }
        },

        async submitTransfer() {
            this.success = '';
            this.error = '';

            try {
                const response = await fetch('/transfers', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        fromAccountId: this.form.fromAccountId,
                        toAccountId: this.form.toAccountId,
                        amount: parseFloat(this.form.amount)
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = data.message;
                    this.form = { fromAccountId: '', toAccountId: '', amount: '' };
                    this.destinationAccounts = [];
                    this.selectedUserId = '';
                    this.fetchClientData();
                } else {
                    this.error = data.details || data.error || 'Erro na transferência';
                }
            } catch {
                this.error = 'Erro na requisição';
            }
        }
    },
    mounted() {
        this.fetchClientData();
        this.fetchAllUsers();
    }
}).mount('#app');
