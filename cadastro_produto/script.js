// Botão para voltar à tela inicial
document.getElementById("cadastroProdutoBtn").addEventListener("click", () => {
  window.location.href = "../tela_inicial/index.html";
});

// Recupera produtos do localStorage
function carregarProdutos() {
  //carregamento seguro
  let dados = JSON.parse(localStorage.getItem("produtos")) || [];

  dados = dados.filter(p => p && typeof p === "object" && p.nome);

  return dados;
}

// Salva produtos no localStorage
function salvarProdutos(produtos) {
  localStorage.setItem("produtos", JSON.stringify(produtos));
}

// Exibe produtos na tabela
function exibirProdutos() {
  const produtos = carregarProdutos();
  const tabela = document.querySelector("#tabelaProdutos tbody");
  tabela.innerHTML = "";

  produtos.forEach((produto, index) => {
    const tr = document.createElement("tr");

    tr.innerHTML = `
      <td>${index + 1}</td>
      <td>${produto.nome}</td>
      <td>R$ ${produto.preco.toFixed(2)}</td>
      <td>${produto.quantidade}</td>
      <td>${produto.validade || "-"}</td>
      <td>${produto.cor || "-"}</td>
      <td>${produto.textura || "-"}</td>
      <td>${produto.material || "-"}</td>
      <td>${produto.peso ? produto.peso + " " + produto.unidade : "-"}</td>
      <td>${produto.unidade || "-"}</td>
      <td>${produto.aplicacao || "-"}</td>
      <td>
        <button onclick="editarProduto(${index})">Editar</button>
        <button onclick="excluirProduto(${index})">Excluir</button>
      </td>
    `;

    tabela.appendChild(tr);
  });
}

// Função para adicionar ou editar produto
function adicionarOuEditarProduto() {
  const id = document.getElementById("produtoId").value;
  const nome = document.getElementById("nomeProduto").value.trim();
  const preco = parseFloat(document.getElementById("precoProduto").value);
  const quantidade = parseInt(document.getElementById("quantidadeProduto").value);

  const validade = document.getElementById("validadeProduto").value;
  const cor = document.getElementById("corProduto").value.trim();
  const textura = document.getElementById("texturaProduto").value.trim();
  const material = document.getElementById("materialProduto").value.trim();
  const peso = parseFloat(document.getElementById("pesoProduto").value);
  const unidade = document.getElementById("unidadeMedida").value;
  const aplicacao = document.getElementById("aplicacaoProduto").value;

  // 🔍 Validações básicas
  if (!nome) {
    alert("Por favor, insira o nome do produto!");
    return false;
  }

  if (isNaN(preco) || preco <= 0) {
    alert("Insira um preço válido (maior que zero).");
    return false;
  }

  if (isNaN(quantidade) || quantidade < 0) {
    alert("Insira uma quantidade válida (zero ou maior).");
    return false;
  }

  if (!unidade) {
    alert("Selecione a unidade de medida!");
    return false;
  }

  if (!aplicacao) {
    alert("Selecione a aplicação do produto!");
    return false;
  }

  const produtos = carregarProdutos();

  const novoProduto = {
    nome,
    preco,
    quantidade,
    validade,
    cor,
    textura,
    material,
    peso: isNaN(peso) ? null : peso,
    unidade,
    aplicacao
  };

  if (id) {
    produtos[id] = novoProduto;
    alert("Produto atualizado com sucesso!");
  } else {
    produtos.push(novoProduto);
    alert("Produto cadastrado com sucesso!");
  }

  salvarProdutos(produtos);
  exibirProdutos();
  limparFormulario();
  return false;
}

// Editar produto
function editarProduto(index) {
  const produtos = carregarProdutos();
  const produto = produtos[index];

  document.getElementById("produtoId").value = index;
  document.getElementById("nomeProduto").value = produto.nome;
  document.getElementById("precoProduto").value = produto.preco;
  document.getElementById("quantidadeProduto").value = produto.quantidade;
  document.getElementById("validadeProduto").value = produto.validade || "";
  document.getElementById("corProduto").value = produto.cor || "";
  document.getElementById("texturaProduto").value = produto.textura || "";
  document.getElementById("materialProduto").value = produto.material || "";
  document.getElementById("pesoProduto").value = produto.peso || "";
  document.getElementById("unidadeMedida").value = produto.unidade || "";
  document.getElementById("aplicacaoProduto").value = produto.aplicacao || "";
  document.getElementById("botaoCadastrar").textContent = "Salvar Alterações";
}

// Excluir produto
function excluirProduto(index) {
  const produtos = carregarProdutos();

  if (confirm(`Tem certeza que deseja excluir o produto "${produtos[index].nome}"?`)) {
    produtos.splice(index, 1);
    salvarProdutos(produtos);
    exibirProdutos();
    alert("Produto excluído com sucesso!");
  }
}

// Buscar produto
function buscarProduto() {
  const termo = document.getElementById("busca").value.toLowerCase();
  const produtos = carregarProdutos();
  const tabela = document.querySelector("#tabelaProdutos tbody");
  tabela.innerHTML = "";

  produtos
    .filter(produto => produto.nome.toLowerCase().includes(termo))
    .forEach((produto, index) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${index + 1}</td>
        <td>${produto.nome}</td>
        <td>R$ ${produto.preco.toFixed(2)}</td>
        <td>${produto.quantidade}</td>
        <td>${produto.validade || "-"}</td>
        <td>${produto.cor || "-"}</td>
        <td>${produto.textura || "-"}</td>
        <td>${produto.material || "-"}</td>
        <td>${produto.peso ? produto.peso + " " + produto.unidade : "-"}</td>
        <td>${produto.unidade || "-"}</td>
        <td>${produto.aplicacao || "-"}</td>
        <td>
          <button onclick="editarProduto(${index})">Editar</button>
          <button onclick="excluirProduto(${index})">Excluir</button>
        </td>
      `;
      tabela.appendChild(tr);
    });
}

// Limpar formulário
function limparFormulario() {
  document.querySelector("form").reset();
  document.getElementById("produtoId").value = "";
  document.getElementById("botaoCadastrar").textContent = "Cadastrar";
}

// Inicializa
document.addEventListener("DOMContentLoaded", exibirProdutos);
