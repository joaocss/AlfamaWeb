document.addEventListener('DOMContentLoaded', function () {
    const profileForm = document.getElementById('profileForm');
    const fotoPerfilInput = document.getElementById('fotoPerfil'); // Novo elemento
    let fotoPerfilFile = null;

    async function uploadFotoPerfil(file) {
        const formData = new FormData();
        formData.append('fotoPerfil', file);
    
        try {
            const response = await fetch('../controllers/UserController.php?action=uploadPhoto', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Erro no upload:', error);
            return { success: false, message: 'Erro na comunicação com o servidor' };
        }
    }

    // Adiciona pré-visualização da foto (nova funcionalidade)
    if (fotoPerfilInput) {
        fotoPerfilInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                fotoPerfilFile = this.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const form = this;

            // Validação básica no frontend (mantida igual)
            if (!form.elements['email'].value.includes('@')) {
                showToast('Por favor, insira um e-mail válido.', 'danger');
                return;
            }

            // Desabilita o botão durante o envio (mantido igual)
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            try {
                // DECISÃO CRÍTICA: Verifica se há foto para enviar
                if (fotoPerfilFile) {
                    // Se tem foto, usa FormData (novo)
                    const formData = new FormData();
                    formData.append('profileData', JSON.stringify({
                        nome: form.elements['nome'].value,
                        email: form.elements['email'].value,
                        telefone: form.elements['telefone'].value,
                        endereco: form.elements['endereco'].value,
                        cpf: form.elements['cpf'].value,
                        empresa: form.elements['empresa'].value
                    }));
                    formData.append('fotoPerfil', fotoPerfilFile);

                    var response = await fetch('../controllers/UserController.php?action=update', {
                        method: 'POST',
                        body: formData
                        // Não definir Content-Type para FormData!
                    });
                } else {
                    // Se não tem foto, mantém o JSON original (existente)
                    const profileData = {
                        nome: form.elements['nome'].value,
                        email: form.elements['email'].value,
                        telefone: form.elements['telefone'].value,
                        endereco: form.elements['endereco'].value,
                        cpf: form.elements['cpf'].value,
                        empresa: form.elements['empresa'].value
                    };

                    var response = await fetch('../controllers/UserController.php?action=update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(profileData)
                    });
                }

                // Verificação de resposta (mantida igual)
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Erro no servidor');
                }

                if (result.success) {
                    showToast('Perfil atualizado com sucesso!', 'success');
                    // Recarrega após 1 segundo (mantido igual)
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(result.message || 'Erro ao atualizar perfil', 'danger');
                }
            } catch (error) {
                console.error('Erro na atualização:', error);
                showToast(error.message || 'Erro inesperado na atualização', 'danger');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Salvar alterações';
            }
        });
    }

    // Função showToast mantida exatamente igual
    function showToast(message, type = 'primary') {
        const toastEl = document.getElementById('toast');
        const toastBody = document.getElementById('toast-body');

        if (!toastEl || !toastBody) {
            console.error('Elementos do toast não encontrados');
            alert(message);
            return;
        }

        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        toastBody.textContent = message;

        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});