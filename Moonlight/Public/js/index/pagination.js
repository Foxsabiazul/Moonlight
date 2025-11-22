document.addEventListener('DOMContentLoaded', () => {
        const btnCarregarMais = document.getElementById('btn-carregar-mais'); //botao pra chamar a api.
        const listaDeJogos = document.getElementById('lista-de-jogos'); // container de todos os cards carregados
        const statusDiv = document.getElementById('status-carregamento'); // status embaixo do botão de carregar mais
        
        // Define o endpoint da sua API PHP
        const link = "http://localhost/Moonlight/Moonlight_Backend/public";
        const apiEndpoint = link + '/api/jogospaginacao.php'; 

        if (!btnCarregarMais) return; // Garante que o botão existe, se não existir impede o funcionamento.

        btnCarregarMais.addEventListener('click', carregarMaisJogos);

        // Função responsável por criar o HTML do card (isolamento de lógica)
        function criarCardDoJogo(dados, link) {

            const precoNumerico = parseFloat(dados.preco);

            const precoFormatado = precoNumerico.toFixed(2).replace('.', ',');

            const cardHtml = `
                <div class="card-item col-12 col-md-6 text-center">   
                    <div class="card">
                        <img src="${link}/arquivos/${dados.imagem || 'placeholder_item.jpg'}" class="cardImg">
                        <div class="card-content">
                            <p class="white-text">${dados.titulo}</p>
                            <p class="white-text">R$ ${precoFormatado}</p>
                            <p class="card-button-wrapper">
                                <a href="<?= BASE_URL ?>/games/index/${dados.id_games}" class="styledBtn">
                                    Detalhes do jogo
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            `;
            return cardHtml;
        }


        // Função principal usando async/await (Fluxo Sequencial)
        async function carregarMaisJogos() {
            // Pega o número da próxima página a partir do botão
            const nextPage = parseInt(btnCarregarMais.getAttribute('data-next-page'));
            // LÊ o ID da categoria que salvamos no botão
            const categoriaId = btnCarregarMais.dataset.categoriaId;
            
            // Trava o botão e mostra status
            btnCarregarMais.disabled = true;
            statusDiv.textContent = 'Carregando...';

            // Monta a URL para sua API PHP, enviando o parâmetro 'page'
            const url = `${apiEndpoint}?page=${nextPage}`; 

            if (categoriaId) {
                // Se houver um ID, adiciona o parâmetro 'categoria' na URL
                url += `&categoria=${categoriaId}`; 
            }

            try {
                // 1. ESPERA a resposta da sua API PHP
                const response = await fetch(url); 
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                
                // 2. ESPERA a conversão do JSON retornado pelo seu PHP
                const jogos = await response.json(); 
                
                // 3. Processa e Renderiza os jogos
                if (jogos.length > 0) {
                    let htmlParaInjetar = '';
                    
                    // Loop sobre os dados retornados pelo PHP
                    jogos.forEach(jogo => {
                        htmlParaInjetar += criarCardDoJogo(jogo, link);
                    });

                    // Injeta todo o HTML de uma vez (melhor performance)
                    listaDeJogos.insertAdjacentHTML('beforeend', htmlParaInjetar);

                    // 4. ATUALIZA O ESTADO (Próxima página)
                    btnCarregarMais.setAttribute('data-next-page', nextPage + 1);
                    statusDiv.textContent = '';
                    
                    // Se a API retornou menos jogos que o limite (8), chegamos ao fim.
                    if (jogos.length <= 8) { 
                        btnCarregarMais.style.display = 'none';
                        statusDiv.textContent = 'Fim da lista de jogos.';
                    } else {
                        btnCarregarMais.disabled = false; // Libera o botão
                    }

                } else {
                    // Se a API retornou um array vazio, é o fim dos dados
                    btnCarregarMais.style.display = 'none';
                    statusDiv.textContent = 'Fim da lista de jogos.';
                }
                
            } catch (error) {
                // Tratamento de erro
                console.error('Erro ao buscar dados da API:', error);
                statusDiv.textContent = 'Erro ao carregar dados. Tente novamente.';
                btnCarregarMais.disabled = false;
            }
        }
    });

