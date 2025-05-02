document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const loginBtn = this.querySelector('button[type="submit"]');
    const formData = new FormData(this); // Coleta os dados do formulário no formato application/x-www-form-urlencoded

    // Converte os dados do FormData para JSON
    const loginData = {};
    formData.forEach((value, key) => {
        loginData[key] = value;
    });

    // Validação básica no cliente
    if (!loginData.email || !loginData.senha) {
        showToast('Por favor, preencha todos os campos', 'danger');
        return;
    }

    if (!loginData.email.includes('@')) {
        showToast('Por favor, insira um e-mail válido', 'danger');
        return;
    }

    // Desabilita o botão durante o envio
    loginBtn.disabled = true;
    loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Entrando...';

    try {
        // Envia os dados como JSON
        const response = await fetch('../controllers/controller.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(loginData) // Converte o objeto para JSON
        });

        // Verifica se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Resposta não é JSON');
        }

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Erro no servidor');
        }

        if (result.success) {
            showToast('Login realizado com sucesso!', 'success');
            // Redireciona após 1 segundo para ver a mensagem
            setTimeout(() => {
                window.location.href = '../views/atualizar.php';
            }, 1000);
        } else {
            showToast(result.message || 'Erro ao fazer login', 'danger');
        }
    } catch (error) {
        console.error('Erro no login:', error);
        showToast(error.message || 'Erro inesperado', 'danger');
    } finally {
        // Reabilita o botão independente do resultado
        if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.textContent = 'Entrar';
        }
    }
});

function showToast(message, type = 'primary') {
    const toastEl = document.getElementById('toast');
    const toastBody = document.getElementById('toast-body');

    if (!toastEl || !toastBody) {
        console.error('Elementos do toast não encontrados');
        alert(message); // Fallback básico
        return;
    }

    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}