document.getElementById('formCadastro').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const form = this;
    const formData = {
        nome: form.elements['nome'].value,
        email: form.elements['email'].value,
        senha: form.elements['senha'].value,

    };

    // Validação no frontend
    if (!formData.email.includes('@')) {
        showToast('Por favor, insira um e-mail válido.', 'danger');
        return;
    }

    if (formData.senha.length < 8) {
        showToast('A senha deve ter pelo menos 8 caracteres.', 'danger');
        return;
    }

   
    // Desabilita o botão durante o envio
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cadastrando...';

    try {
        const response = await fetch('../controllers/controller.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        // Verifica se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Formato de resposta inválido');
        }

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Erro no servidor');
        }

        if (result.success) {
            showToast('Cadastro realizado com sucesso!', 'success');
            setTimeout(() => {
                window.location.href = 'login.html?registered=true';
            }, 2000);
        } else {
            showToast(result.message || 'Erro ao se cadastrar', 'danger');
        }
    } catch (error) {
        console.error('Erro no cadastro:', error);
        showToast(error.message || 'Erro inesperado no cadastro', 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Criar conta';
    }
});

function showToast(message, type = 'primary') {
    const toastEl = document.getElementById('toast');
    const toastBody = document.getElementById('toast-body');

    if (!toastEl || !toastBody) {
        console.error('Elementos do toast não encontrados');
        alert(message); // Fallback
        return;
    }

    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}