document.addEventListener('DOMContentLoaded', () =>{

    const filtroSelect = document.getElementById('filtro-select');
    const valorOperadorSelect = document.getElementById('valor-operador');
    const termoInput = document.getElementById('termoInput');
    const opcaoIgual = document.getElementById('operador-igual');
    const opcaoMaiorQue = document.getElementById('operador-maiorque');
    const opcaoMenorQue = document.getElementById('operador-menorque');
    const labelOperador = document.getElementById('labelOperador');

    if(filtroSelect.value === 'data_lancamento'){
        opcaoIgual.textContent = "Em";
        opcaoMaiorQue.textContent = "Após";
        opcaoMenorQue.textContent = "Antes de";
        termoInput.type = 'date';
        termoInput.removeAttribute('step');
    } else if(filtroSelect.value === 'preco'){
        opcaoIgual.textContent = "Igual a";
        opcaoMaiorQue.textContent = "Maior que";
        opcaoMenorQue.textContent = "Menor que";
        termoInput.type = 'number';
        // Adiciona o passo de 0.01 para permitir valores decimais
        termoInput.setAttribute('step', '0.01');
    }

    filtroSelect.addEventListener('change', (event) => {

        if(event.target.value === 'preco' || event.target.value === 'data_lancamento'){
            if (event.target.value === 'preco') {
                opcaoIgual.textContent = "Igual a";
                opcaoMaiorQue.textContent = "Maior que";
                opcaoMenorQue.textContent = "Menor que";
                termoInput.type = 'number';
                // Adiciona o passo de 0.01 para permitir valores decimais
                termoInput.setAttribute('step', '0.01');
            } else if (event.target.value === 'data_lancamento') {
                opcaoIgual.textContent = "Em";
                opcaoMaiorQue.textContent = "Após";
                opcaoMenorQue.textContent = "Antes de";
                termoInput.type = 'date';
                termoInput.removeAttribute('step');
            }

            labelOperador.hidden = false;
            valorOperadorSelect.hidden = false;
            labelOperador.removeAttribute('disabled');
            valorOperadorSelect.removeAttribute('disabled');

        } else {
            valorOperadorSelect.hidden = true;
            valorOperadorSelect.setAttribute('disabled', 'disabled');
            labelOperador.hidden = true;
            labelOperador.setAttribute('disabled', 'disabled');
            
            termoInput.type = 'text';
            termoInput.removeAttribute('step');
        }
        
        // Se o valor selecionado for 'prazo', muda o tipo do input para 'date'

    });


});