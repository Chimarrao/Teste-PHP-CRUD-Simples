function exibirUsuarios() {
    fetch('endpoint.php?all_users')
        .then(response => response.json())
        .then(data => {
            const usuariosTbody = document.getElementById('usuarios-tbody');
            usuariosTbody.innerHTML = '';

            data.forEach(usuario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${usuario.user_id}</td>
                <td>${usuario.user_name}</td>
                <td>${usuario.email}</td>
                <td>${usuario.color_name ?? '-'}</td>
                <td>
                    <button class="button is-info" onclick="editarUsuario(${usuario.user_id})">
                        <span class="icon">
                            <i class="fas fa-pencil-alt"></i>
                        </span>
                    </button>
                    <button class="button is-danger" onclick="confirmarExclusao(${usuario.user_id})">
                        <span class="icon">
                            <i class="fas fa-times"></i>
                        </span>
                    </button>
                </td>
            `;
                usuariosTbody.appendChild(tr);
            });
        })
        .catch(error => console.error('Erro ao obter os usuários:', error));
}

function editarUsuario(id) {
    fetch(`endpoint.php?user_id=${id}`)
        .then(response => response.json())
        .then(usuario => {
            fetch('endpoint.php?all_colors')
                .then(response => response.json())
                .then(data => {
                    const coresOptions = data.map(cor => `<option value="${cor.id}" ${usuario.color_id == cor.id ? 'selected' : ''}>${cor.name}</option>`).join('');

                    document.body.insertAdjacentHTML('beforeend',
                        `<div class="modal is-block" id="modal-editar-usuario">
                            <div class="modal-background"></div>
                                <div class="modal-content">
                                    <div class="box">
                                        <h2 class="subtitle">Editar Usuário</h2>
                                        <form id="form-editar-usuario">
                                            <div class="field">
                                                <label class="label">Nome:</label>
                                                <div class="control">
                                                    <input class="input" type="text" placeholder="Nome do usuário" name="nome" value="${usuario.user_name}">
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Email:</label>
                                                <div class="control">
                                                    <input class="input" type="email" placeholder="Email do usuário" name="email" value="${usuario.email}">
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label">Cor:</label>
                                                <div class="control">
                                                    <div class="select">
                                                        <select name="cor">
                                                            <option>Selecione a cor</option>
                                                            ${coresOptions}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field is-grouped">
                                                <div class="control">
                                                    <button class="button is-primary" type="submit">Salvar</button>
                                                </div>
                                                <div class="control">
                                                    <button class="button is-link" type="button" onclick="fecharModal('modal-editar-usuario')">Cancelar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <button class="modal-close is-large" aria-label="close" onclick="fecharModal('modal-editar-usuario')"></button>
                        </div>`
                    );

                    formEditarUsuario(id);
                })
                .catch(error => console.error('Erro ao obter as cores:', error));
        })
        .catch(error => console.error('Erro ao obter as cores:', error));
}

function formEditarUsuario(id) {
    document.getElementById('form-editar-usuario').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const nome = formData.get('nome');
        const email = formData.get('email');
        const cor_id = formData.get('cor');

        fetch('endpoint.php', {
            method: 'PUT',
            body: JSON.stringify({ edit_user: true, id: id, nome: nome, email: email, cor_id: cor_id }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    const modalHtml = `
                        <div class="modal is-active" id="modal-sucesso">
                            <div class="modal-background"></div>
                            <div class="modal-content">
                                <div class="box">
                                    <p>${data.sucesso}</p>
                                    <button class="button" onclick="fecharModal('modal-sucesso')">Ok</button>
                                    </div>
                            </div>
                            <button class="modal-close is-large" aria-label="close" onclick="fecharModal('modal-sucesso')"></button>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                } else if (data.erro) {
                    const modalHtml = `
                        <div class="modal is-active" id="modal-erro">
                            <div class="modal-background"></div>
                            <div class="modal-content">
                                <div class="box">
                                    <p>${data.erro}</p>
                                    <button class="button" onclick="fecharModal('modal-erro')">Ok</button>
                                </div>
                            </div>
                            <button class="modal-close is-large" aria-label="close" onclick="fecharModal('modal-erro')"></button>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                }

                exibirUsuarios();
                fecharModal('modal-editar-usuario');
            })
            .catch(error => console.error('Erro ao editar o usuário:', error));
    });
}

function confirmarExclusao(id) {
    document.body.insertAdjacentHTML('beforeend',
        `<div class="modal is-block" id="modal-exclusao">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="box">
                    <p>Você tem certeza que deseja excluir este usuário?</p>
                    <div class="buttons">
                        <button class="button is-danger" onclick="excluirUsuario()">Sim</button>
                        <button class="button" onclick="fecharModal('modal-exclusao')">Não</button>
                    </div>
                </div>
            </div>
            <button class="modal-close is-large" aria-label="close" onclick="fecharModal('modal-exclusao')"></button>
        </div>`
    );
}

function fecharModal(id) {
    const element = document.getElementById(id);
    if (element) {
        element.remove();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    exibirUsuarios();
});