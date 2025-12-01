// Botão para voltar à tela inicial
document.getElementById("gestaoEstoqueBtn").addEventListener("click", () => {
  window.location.href = "../tela_inicial/index.php";
});

// ----- Carregar produtos do "banco" (localStorage) -----
function carregarProdutos() {
  return JSON.parse(localStorage.getItem("produtos")) || [];
}

function salvarProdutos(produtos) {
  localStorage.setItem("produtos", JSON.stringify(produtos));
}

// ----- Preencher o select de produtos -----
function preencherSelectProdutos() {
  const produtos = carregarProdutos();
  const select = document.getElementById("produtoSelect");

  // Ordena produtos por nome (ordem alfabética)
  produtos.sort((a, b) => a.nome.localeCompare(b.nome));

  select.innerHTML = "<option value=''>-- Selecione um produto --</option>";
  produtos.forEach((produto) => {
    const option = document.createElement("option");
    option.value = produto.nome; // usa o nome como identificador
    option.textContent = produto.nome;
    select.appendChild(option);
  });

  exibirEstoque(produtos);
}

// ----- Exibir tabela de estoque -----
function exibirEstoque(produtos) {
  const tabela = document.querySelector("#tabelaEstoque tbody");
  tabela.innerHTML = "";

  produtos.forEach((p) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${p.nome}</td>
      <td>R$ ${p.preco.toFixed(2)}</td>
      <td>${p.quantidade}</td>
      <td>${p.validade || "-"}</td>
      <td>${p.cor || "-"}</td>
      <td>${p.textura || "-"}</td>
      <td>${p.material || "-"}</td>
      <td>${p.peso || "-"}</td>
      <td>${p.unidade || "-"}</td>
      <td>${p.aplicacao || "-"}</td>
    `;
    tabela.appendChild(tr);
  });
}

// ----- Registrar movimentação -----
function registrarMovimentacao() {
  const nomeProdutoSelecionado = document.getElementById("produtoSelect").value;
  const tipo = document.getElementById("tipoMovimentacao").value;
  const quantidade = parseInt(document.getElementById("quantidadeMov").value);
  const data = document.getElementById("dataMovimentacao").value;

  // Validação de campos
  if (!nomeProdutoSelecionado || !tipo || !quantidade || !data) {
    alert("Por favor, preencha todos os campos corretamente!");
    return;
  }

  const produtos = carregarProdutos();
  const produto = produtos.find((p) => p.nome === nomeProdutoSelecionado);

  if (!produto) {
    alert("Produto não encontrado!");
    return;
  }

  // Lógica de movimentação
  if (tipo === "entrada") {
    produto.quantidade += quantidade;
  } else if (tipo === "saida") {
    if (produto.quantidade < quantidade) {
      alert("Estoque insuficiente para esta saída!");
      return;
    }
    produto.quantidade -= quantidade;
  }

  // Verificação automática de estoque mínimo (após qualquer movimentação)
  const estoqueMinimo = 5;
  if (produto.quantidade <= 5) {
    alert(`⚠️ Atenção: o estoque de "${produto.nome}" está abaixo ou igual ao mínimo (${estoqueMinimo}).`);
  }

  // Salva e atualiza exibição
  salvarProdutos(produtos);
  alert("Movimentação registrada com sucesso!");
  preencherSelectProdutos();

  // Limpa os campos
  document.getElementById("produtoSelect").value = "";
  document.getElementById("tipoMovimentacao").value = "";
  document.getElementById("quantidadeMov").value = "";
  document.getElementById("dataMovimentacao").value = "";
}

// Inicializa a página
document.addEventListener("DOMContentLoaded", preencherSelectProdutos);
