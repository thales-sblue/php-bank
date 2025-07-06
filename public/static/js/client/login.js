const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                username: '',
                password: ''
            },
            error: ''
        };
    },
    methods: {
        async submitLogin() {
            try {
                const response = await fetch('/clients/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.replace('/clients/auth');
                } else {
                    this.error = data.details || 'Credenciais inválidas';
                }
            } catch (error) {
                this.error = 'Erro na requisição';
            }
        }
    }
}).mount('#app');