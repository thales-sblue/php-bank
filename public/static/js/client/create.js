const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                username: '',
                password: '',
                name: '',
                cpfcnpj: '',
                email: ''
            },
            success: ''
        }
    },
    methods: {
        async submitForm() {
            try {
                const response = await fetch('/clients', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.success = data.message || 'Cadastro realizado com sucesso!';
                    this.form = {
                        username: '',
                        password: '',
                        name: '',
                        cpfcnpj: '',
                        email: ''
                    };
                    setTimeout(() => {
                        window.location.replace('/clients/login');
                    }, 1500);
                } else {
                    alert(data.error || 'Erro ao cadastrar');
                }
            } catch (error) {
                alert('Erro na requisição');
            }
        }
    }
}).mount('#app');