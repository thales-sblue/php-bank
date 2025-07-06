const { createApp } = Vue;

createApp({
    data() {
        return {
            client: null,
            accounts: [],
            selectedAccountId: '',
            selectedAccountType: '',
            transactions: [],
            error: ''
        }
    },
    methods: {
        async fetchClientData() {
            try {
                const response = await fetch('/clients/home');
                const data = await response.json();

                if (!response.ok || !data.client) {
                    this.error = data.error || 'Erro ao carregar dados';
                    return;
                }

                this.client = data.client;
                this.accounts = data.accounts || [];
            } catch (err) {
                this.error = 'Erro ao carregar dados';
            }
        },

        async selectAccount(account) {
            this.selectedAccountId = account.id;
            this.selectedAccountType = account.type;
            await this.fetchTransactions(account.id);
        },

        async fetchTransactions(accountId) {
            try {
                const response = await fetch(`/transactions/${accountId}`);
                const data = await response.json();

                if (response.ok) {
                    this.transactions = data;
                    this.error = '';
                } else {
                    this.transactions = [];
                    this.error = data.error || 'Erro ao carregar transações';
                }
            } catch {
                this.transactions = [];
                this.error = 'Erro ao buscar transações';
            }
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
        }
    },
    mounted() {
        this.fetchClientData();
    }
}).mount('#app');