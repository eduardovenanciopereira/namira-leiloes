function renderCardsAutomatico(listaImoveisPorIndice) {
    const containers = document.querySelectorAll(".cards-container");
    if (!containers.length) return;

    const infoQuantity = document.getElementById("info-quantity");
    if (!infoQuantity) return;

    let totalImoveisRenderizados = 0;

    containers.forEach((container, index) => {
        container.innerHTML = "";

        const listaImoveis = listaImoveisPorIndice[index];
        if (!listaImoveis || !listaImoveis.length) return;

        listaImoveis.forEach(imovel => {
            totalImoveisRenderizados++;

            const card = document.createElement("div");
            card.className = "card";
            card.style.cursor = "pointer";
            card.addEventListener("click", () => {
                const slug = imovel.slug || "imovel";
                const id = imovel.id || "0";
                window.location.href = `/imovel/${slug}-${id}`;
            });

            const img = document.createElement("img");
            img.src = imovel.imagem;
            img.alt = imovel.titulo || "Imóvel";
            card.appendChild(img);

            const body = document.createElement("div");
            body.className = "card-body";

            const title = document.createElement("h3");
            title.className = "card-title";
            title.textContent = imovel.titulo;
            body.appendChild(title);

            const location = document.createElement("p");
            location.className = "card-location";
            location.textContent = `${imovel.bairro}, ${imovel.uf}`;
            body.appendChild(location);

            const price = document.createElement("p");
            price.className = "card-price";
            price.innerHTML = `R$ ${imovel.valor_venda.toLocaleString('pt-BR')} <span class="card-discount">${imovel.desconto_percentual}% off</span>`;
            body.appendChild(price);

            const desc = document.createElement("p");
            desc.className = "card-desc";
            desc.textContent = imovel.descricao;
            body.appendChild(desc);

            const actions = document.createElement("div");
            actions.className = "card-actions";

            const btnMap = document.createElement("a");
            btnMap.href = imovel.google_maps;
            btnMap.className = "btn-map";
            btnMap.textContent = "Abrir no Google Maps";
            btnMap.target = "_blank";
            btnMap.addEventListener("click", e => e.stopPropagation());

            const btnFavContainer = document.createElement("div");
            btnFavContainer.className = "btn-fav-container";

            const btnAddFav = document.createElement("button");
            btnAddFav.className = "btn-fav add-fav";
            btnAddFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 109.57" width="24" height="24" fill="currentColor">
    <g>
        <path d="M65.46,19.57c-0.68,0.72-1.36,1.45-2.2,2.32l-2.31,2.41l-2.4-2.33c-0.71-0.69-1.43-1.4-2.13-2.09
            c-7.42-7.3-13.01-12.8-24.52-12.95c-0.45-0.01-0.93,0-1.43,0.02c-6.44,0.23-12.38,2.6-16.72,6.65
            c-4.28,4-7.01,9.67-7.1,16.57c-0.01,0.43,0,0.88,0.02,1.37c0.69,19.27,19.13,36.08,34.42,50.01
            c2.95,2.69,5.78,5.27,8.49,7.88l11.26,10.85l14.15-14.04c2.28-2.26,4.86-4.73,7.62-7.37
            c4.69-4.5,9.91-9.49,14.77-14.52c3.49-3.61,6.8-7.24,9.61-10.73c2.76-3.42,5.02-6.67,6.47-9.57
            c2.38-4.76,3.13-9.52,2.62-13.97c-0.5-4.39-2.23-8.49-4.82-11.99c-2.63-3.55-6.13-6.49-10.14-8.5
            C96.5,7.29,91.21,6.2,85.8,6.82C76.47,7.9,71.5,13.17,65.46,19.57z M60.77,14.85C67.67,7.54,73.4,1.55,85.04,0.22
            c6.72-0.77,13.3,0.57,19.03,3.45c4.95,2.48,9.27,6.1,12.51,10.47c3.27,4.42,5.46,9.61,6.1,15.19
            c0.65,5.66-0.29,11.69-3.3,17.69c-1.7,3.39-4.22,7.03-7.23,10.76c-2.95,3.66-6.39,7.44-10,11.17
            C97.2,74.08,91.94,79.12,87.2,83.66c-2.77,2.65-5.36,5.13-7.54,7.29L63.2,107.28l-2.31,2.29l-2.34-2.25
            l-13.6-13.1c-2.49-2.39-5.37-5.02-8.36-7.75C20.38,71.68,0.81,53.85,0.02,31.77C0,31.23,0,30.67,0,30.09
            c0.12-8.86,3.66-16.18,9.21-21.36c5.5-5.13,12.97-8.13,21.01-8.42c0.55-0.02,1.13-0.03,1.74-0.02
            C46,0.48,52.42,6.63,60.77,14.85z"/>
    </g>
</svg>`;
            btnAddFav.addEventListener("click", e => e.stopPropagation());

            const btnRemoveFav = document.createElement("button");
            btnRemoveFav.className = "btn-fav remove-fav";
            btnRemoveFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110.61 122.88" width="24" height="24" fill="currentColor">
    <path d="M39.27,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Zm63.6-19.86L98,103a22.29,22.29,0,0,1-6.33,14.1,19.41,19.41,0,0,1-13.88,5.78h-45a19.4,19.4,0,0,1-13.86-5.78l0,0A22.31,22.31,0,0,1,12.59,103L7.74,38.78H0V25c0-3.32,1.63-4.58,4.84-4.58H27.58V10.79A10.82,10.82,0,0,1,38.37,0H72.24A10.82,10.82,0,0,1,83,10.79v9.62h23.35a6.19,6.19,0,0,1,1,.06A3.86,3.86,0,0,1,110.59,24c0,.2,0,.38,0,.57V38.78Zm-9.5.17H17.24L22,102.3a12.82,12.82,0,0,0,3.57,8.1l0,0a10,10,0,0,0,7.19,3h45a10.06,10.06,0,0,0,7.19-3,12.8,12.8,0,0,0,3.59-8.1L93.37,39ZM71,20.41V12.05H39.64v8.36ZM61.87,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Z"/>
</svg>`;
            btnRemoveFav.addEventListener("click", e => e.stopPropagation());

            btnFavContainer.appendChild(btnAddFav);
            btnFavContainer.appendChild(btnRemoveFav);

            actions.appendChild(btnFavContainer);
            actions.appendChild(btnMap);

            body.appendChild(actions);
            card.appendChild(body);
            container.appendChild(card);
        });
    });

    // Texto final correto
    infoQuantity.textContent = `${totalImoveisRenderizados} disponíveis`;
}

function renderCardsComFiltros(imoveis, paginas) {
    const cardsContent = document.getElementById("cards-content");
    if (!cardsContent) return;

    // Limpa todo o conteúdo
    cardsContent.innerHTML = "";
    
    const infoQuantity = document.getElementById("info-quantity");
    if (!infoQuantity) return;
    
    infoQuantity.textContent = `${imoveis.length * paginas} disponíveis`
    
    if (!imoveis || !imoveis.length) return;

    const MAX_ROWS = 7;
    const CARDS_PER_ROW = 8;

    const totalRows = Math.min(
        Math.ceil(imoveis.length / CARDS_PER_ROW),
        MAX_ROWS
    );

    let index = 0;

    for (let r = 0; r < totalRows; r++) {
        // Cria a div cards-row
        const row = document.createElement("div");
        row.className = "cards-row";

        // Cria a div cards-container dentro da row
        const container = document.createElement("div");
        container.className = "cards-container";

        // Adiciona até 8 cards por container
        for (let c = 0; c < CARDS_PER_ROW && index < imoveis.length; c++, index++) {
            const imovel = imoveis[index];

            const card = document.createElement("div");
            card.className = "card";
            card.style.cursor = "pointer";
            card.addEventListener("click", () => {
                const slug = imovel.slug || "imovel";
                const id = imovel.id || "0";
                window.location.href = `/imovel/${slug}-${id}`;
            });

            const img = document.createElement("img");
            img.src = imovel.imagem;
            img.alt = imovel.titulo || "Imóvel";
            card.appendChild(img);

            const body = document.createElement("div");
            body.className = "card-body";

            const title = document.createElement("h3");
            title.className = "card-title";
            title.textContent = imovel.titulo;
            body.appendChild(title);

            const location = document.createElement("p");
            location.className = "card-location";
            location.textContent = `${imovel.bairro}, ${imovel.uf}`;
            body.appendChild(location);

            const price = document.createElement("p");
            price.className = "card-price";
            price.innerHTML = `R$ ${imovel.valor_venda.toLocaleString('pt-BR')} <span class="card-discount">${imovel.desconto_percentual}% off</span>`;
            body.appendChild(price);

            const desc = document.createElement("p");
            desc.className = "card-desc";
            desc.textContent = imovel.descricao;
            body.appendChild(desc);

            const actions = document.createElement("div");
            actions.className = "card-actions";

            const btnMap = document.createElement("a");
            btnMap.href = imovel.google_maps;
            btnMap.className = "btn-map";
            btnMap.textContent = "Abrir no Google Maps";
            btnMap.target = "_blank";
            btnMap.addEventListener("click", e => e.stopPropagation());

            const btnFavContainer = document.createElement("div");
            btnFavContainer.className = "btn-fav-container";

            const btnAddFav = document.createElement("button");
            btnAddFav.className = "btn-fav add-fav";
            btnAddFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 109.57" width="24" height="24" fill="currentColor">
    <g>
        <path d="M65.46,19.57c-0.68,0.72-1.36,1.45-2.2,2.32l-2.31,2.41l-2.4-2.33c-0.71-0.69-1.43-1.4-2.13-2.09
            c-7.42-7.3-13.01-12.8-24.52-12.95c-0.45-0.01-0.93,0-1.43,0.02c-6.44,0.23-12.38,2.6-16.72,6.65
            c-4.28,4-7.01,9.67-7.1,16.57c-0.01,0.43,0,0.88,0.02,1.37c0.69,19.27,19.13,36.08,34.42,50.01
            c2.95,2.69,5.78,5.27,8.49,7.88l11.26,10.85l14.15-14.04c2.28-2.26,4.86-4.73,7.62-7.37
            c4.69-4.5,9.91-9.49,14.77-14.52c3.49-3.61,6.8-7.24,9.61-10.73c2.76-3.42,5.02-6.67,6.47-9.57
            c2.38-4.76,3.13-9.52,2.62-13.97c-0.5-4.39-2.23-8.49-4.82-11.99c-2.63-3.55-6.13-6.49-10.14-8.5
            C96.5,7.29,91.21,6.2,85.8,6.82C76.47,7.9,71.5,13.17,65.46,19.57z M60.77,14.85C67.67,7.54,73.4,1.55,85.04,0.22
            c6.72-0.77,13.3,0.57,19.03,3.45c4.95,2.48,9.27,6.1,12.51,10.47c3.27,4.42,5.46,9.61,6.1,15.19
            c0.65,5.66-0.29,11.69-3.3,17.69c-1.7,3.39-4.22,7.03-7.23,10.76c-2.95,3.66-6.39,7.44-10,11.17
            C97.2,74.08,91.94,79.12,87.2,83.66c-2.77,2.65-5.36,5.13-7.54,7.29L63.2,107.28l-2.31,2.29l-2.34-2.25
            l-13.6-13.1c-2.49-2.39-5.37-5.02-8.36-7.75C20.38,71.68,0.81,53.85,0.02,31.77C0,31.23,0,30.67,0,30.09
            c0.12-8.86,3.66-16.18,9.21-21.36c5.5-5.13,12.97-8.13,21.01-8.42c0.55-0.02,1.13-0.03,1.74-0.02
            C46,0.48,52.42,6.63,60.77,14.85z"/>
    </g>
</svg>`;
            btnAddFav.addEventListener("click", e => e.stopPropagation());

            const btnRemoveFav = document.createElement("button");
            btnRemoveFav.className = "btn-fav remove-fav";
            btnRemoveFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110.61 122.88" width="24" height="24" fill="currentColor">
    <path d="M39.27,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Zm63.6-19.86L98,103a22.29,22.29,0,0,1-6.33,14.1,19.41,19.41,0,0,1-13.88,5.78h-45a19.4,19.4,0,0,1-13.86-5.78l0,0A22.31,22.31,0,0,1,12.59,103L7.74,38.78H0V25c0-3.32,1.63-4.58,4.84-4.58H27.58V10.79A10.82,10.82,0,0,1,38.37,0H72.24A10.82,10.82,0,0,1,83,10.79v9.62h23.35a6.19,6.19,0,0,1,1,.06A3.86,3.86,0,0,1,110.59,24c0,.2,0,.38,0,.57V38.78Zm-9.5.17H17.24L22,102.3a12.82,12.82,0,0,0,3.57,8.1l0,0a10,10,0,0,0,7.19,3h45a10.06,10.06,0,0,0,7.19-3,12.8,12.8,0,0,0,3.59-8.1L93.37,39ZM71,20.41V12.05H39.64v8.36ZM61.87,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Z"/>
</svg>`;
            btnRemoveFav.addEventListener("click", e => e.stopPropagation());

            btnFavContainer.appendChild(btnAddFav);
            btnFavContainer.appendChild(btnRemoveFav);

            actions.appendChild(btnFavContainer);
            actions.appendChild(btnMap);

            body.appendChild(actions);
            card.appendChild(body);
            container.appendChild(card);
        }

        row.appendChild(container);
        cardsContent.appendChild(row);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    localStorage.setItem("lastPage", window.location.pathname);
    
    const btnFilter = document.getElementById("btn-filter");
    const filtersContainer = document.getElementById("filters-container");

    if (btnFilter && filtersContainer) {
        btnFilter.addEventListener("click", function () {
            this.classList.toggle("active");
            filtersContainer.classList.toggle("active");
        });
    }

    const selectUf = document.getElementById("filter-uf");
    const selectCidade = document.getElementById("filter-cidade");
    const selectBairro = document.getElementById("filter-bairro");
    const selectModalidade = document.getElementById("filter-modalidade");

    const priceMin = document.getElementById("price-min");
    const priceMax = document.getElementById("price-max");
    const percentMin = document.getElementById("percent-min");
    const percentMax = document.getElementById("percent-max");

    const btnApply = document.querySelector(".btn-apply");

    /* =======================
       CARREGAMENTO DOS CARDS
       (NÃO ALTERADO)
    ======================= */
    const routeParams = window.ROUTE ? window.ROUTE.params : null;
    
    const pageContainer = document.getElementById("page-container");
    
    if (!routeParams) {
        if (pageContainer) {
            pageContainer.remove();
        }
        
        const urls = [
            "http://localhost:7777/api/imoveis/sp/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/rj/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/mg/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/pr/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/rs/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/sc/all/all/30000/150000/15/100/all/1/8",
            "http://localhost:7777/api/imoveis/df/all/all/30000/150000/15/100/all/1/8"
        ];

        try {
            const responses = await Promise.all(urls.map(url => fetch(url)));
            const results = await Promise.all(responses.map(res => res.json()));
            const listaImoveisPorIndice = results.map(r => r.data);
            renderCardsAutomatico(listaImoveisPorIndice);
        } catch (e) {
            console.error("Erro ao carregar cards:", e);
        }
        
        let filtrosData = null;
        
        try {
            const response = await fetch("http://localhost:7777/api/filtros-imoveis");
            filtrosData = await response.json();
        } catch (e) {
            console.error("Erro ao carregar filtros:", e);
            return;
        }
            
        filtrosData.ufs.forEach(uf => {
            const opt = document.createElement("option");
            opt.value = uf.toLowerCase();
            opt.textContent = uf.toUpperCase();
            selectUf.appendChild(opt);
        });
        
        filtrosData.modalidades_venda.forEach(m => {
            const opt = document.createElement("option");
            opt.value = m.toLowerCase();
            opt.textContent = m;
            selectModalidade.appendChild(opt);
        });
        
        selectUf.addEventListener("change", () => {
            const selecionarUf = (ufSelecionada) => {
                for (const item of filtrosData.ufs) {
                    if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const uf = selecionarUf(selectUf.value);
        
            selectCidade.innerHTML = `<option value="">Todas as cidades</option>`;
            selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
            selectCidade.disabled = true;
            selectBairro.disabled = true;
        
            if (!uf || !filtrosData.cidades[uf]) return;
        
            filtrosData.cidades[uf].forEach(c => {
                const opt = document.createElement("option");
                opt.value = c.toLowerCase();
                opt.textContent = c;
                selectCidade.appendChild(opt);
            });
        
            selectCidade.disabled = false;
        });
        
        selectCidade.addEventListener("change", () => {
            const selecionarUf = (ufSelecionada) => {
                for (const item of filtrosData.ufs) {
                    if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const uf = selecionarUf(selectUf.value);
            
            const selecionarCidade = (cidadeSelecionada) => {
                for (const item of filtrosData.cidades[uf]) {
                    if (item.toLowerCase() === cidadeSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const cidade = selecionarCidade(selectCidade.value);
        
            selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
            selectBairro.disabled = true;
        
            if (!cidade || !filtrosData.bairros[cidade]) return;
        
            filtrosData.bairros[cidade].forEach(b => {
                const opt = document.createElement("option");
                opt.value = b.toLowerCase();
                opt.textContent = b;
                selectBairro.appendChild(opt);
            });
        
            selectBairro.disabled = false;
        });
    } else {
        const ufParam = (routeParams[0] ?? "all").toLowerCase();
        const cidadeParam = (routeParams[1] ? decodeURIComponent(routeParams[1]) : "all").toLowerCase().replace(/-/g, " ");
        const bairroParam = (routeParams[2] ? decodeURIComponent(routeParams[2]) : "all").toLowerCase().replace(/-/g, " ");
        const minAvaliacaoParam = routeParams[3] ?? "0";
        const maxAvaliacaoParam = routeParams[4] ?? "0";
        const minPorcentagemParam = routeParams[5] ?? "0";
        const maxPorcentagemParam = routeParams[6] ?? "0";
        const modalidadeVendaParam = (routeParams[7] ? decodeURIComponent(routeParams[7]) : "all").toLowerCase().replace(/-/g, " ");
        const paginaAtualParam = routeParams[8] ?? "1";
    
        const url = `http://localhost:7777/api/imoveis/${ufParam}/${cidadeParam}/${bairroParam}/${minAvaliacaoParam}/${maxAvaliacaoParam}/${minPorcentagemParam}/${maxPorcentagemParam}/${modalidadeVendaParam}/${paginaAtualParam}/56`;
    
        try {
            const textTitle = document.getElementById("text-title");
            
            textTitle.textContent = "Resultados encontrados";

            const response = await fetch(url);
            const result = await response.json();
            
            renderCardsComFiltros(result.data, result.paginas);
            
            const maxPage = Number(result.paginas);
            const paginaAtual = Number(paginaAtualParam);
            
            if (pageContainer && maxPage >= 1) {
                pageContainer.innerHTML = "";
            
                const criarBotao = (label, page, disabled = false, active = false, isNav = false) => {
                    const btn = document.createElement("button");
                    btn.innerHTML = label;
                    btn.classList.add("btn-page");
            
                    if (isNav) btn.classList.add("nav");
                    if (active) btn.classList.add("active");
            
                    if (disabled) {
                        btn.disabled = true;
                        return btn;
                    }
            
                    btn.onclick = () => {
                        window.location.href =
                            `/imoveis/${ufParam}/${cidadeParam}/${bairroParam}/${minAvaliacaoParam}/${maxAvaliacaoParam}/${minPorcentagemParam}/${maxPorcentagemParam}/${modalidadeVendaParam}/${page}`;
                    };
            
                    return btn;
                };
            
                // << Primeira página
                pageContainer.appendChild(
                    criarBotao(`
<svg
    width="15"
    height="15"
    viewBox="0 0 120.64 122.88"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
>
    <path
        d="M66.6,108.91c1.55,1.63,2.31,3.74,2.28,5.85c-0.03,2.11-0.84,4.2-2.44,5.79l-0.12,0.12c-1.58,1.5-3.6,2.23-5.61,2.2c-2.01-0.03-4.02-0.82-5.55-2.37C37.5,102.85,20.03,84.9,2.48,67.11c-0.07-0.05-0.13-0.1-0.19-0.16C0.73,65.32-0.03,63.19,0,61.08c0.03-2.11,0.85-4.21,2.45-5.8l0.27-0.26C20.21,37.47,37.65,19.87,55.17,2.36C56.71,0.82,58.7,0.03,60.71,0c2.01-0.03,4.03,0.7,5.61,2.21l0.15,0.15c1.57,1.58,2.38,3.66,2.41,5.76c0.03,2.1-0.73,4.22-2.28,5.85L19.38,61.23L66.6,108.91z
           M118.37,106.91c1.54,1.62,2.29,3.73,2.26,5.83c-0.03,2.11-0.84,4.2-2.44,5.79l-0.12,0.12c-1.57,1.5-3.6,2.23-5.61,2.21c-2.01-0.03-4.02-0.82-5.55-2.37C89.63,101.2,71.76,84.2,54.24,67.12c-0.07-0.05-0.14-0.11-0.21-0.17c-1.55-1.63-2.31-3.76-2.28-5.87c0.03-2.11,0.85-4.21,2.45-5.8C71.7,38.33,89.27,21.44,106.8,4.51l0.12-0.13c1.53-1.54,3.53-2.32,5.54-2.35c2.01-0.03,4.03,0.7,5.61,2.21l0.15,0.15c1.57,1.58,2.38,3.66,2.41,5.76c0.03,2.1-0.73,4.22-2.28,5.85L71.17,61.23L118.37,106.91z"
        fill="currentColor"
    />
</svg>`, 1, paginaAtual === 1, false, true)
                );
            
                // < Página anterior
                pageContainer.appendChild(
                    criarBotao(`
<svg
    width="15"
    height="15"
    viewBox="0 0 66.91 122.88"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
>
    <path
        d="M64.96,111.2c2.65,2.73,2.59,7.08-0.13,9.73c-2.73,2.65-7.08,2.59-9.73-0.14L1.97,66.01c-2.65-2.74-2.59-7.1,0.15-9.76c0.08-0.08,0.16-0.15,0.24-0.22L55.1,2.09c2.65-2.73,7-2.79,9.73-0.14c2.73,2.65,2.78,7.01,0.13,9.73L16.5,61.23L64.96,111.2z"
        fill="currentColor"
    />
</svg>`, paginaAtual - 1, paginaAtual === 1, false, true)
                );
            
                // ===== NÚMEROS (REGRA RÍGIDA: máx. 1 de cada lado) =====
                const maxSide = 1;
            
                const start = Math.max(1, paginaAtual - maxSide);
                const end = Math.min(maxPage, paginaAtual + maxSide);
            
                for (let i = start; i <= end; i++) {
                    pageContainer.appendChild(
                        criarBotao(i, i, false, i === paginaAtual)
                    );
                }
            
                // > Próxima página
                pageContainer.appendChild(
                    criarBotao(`
<svg
    width="15"
    height="15"
    viewBox="0 0 66.91 122.88"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
>
    <path
        d="M1.95,111.2c-2.65,2.72-2.59,7.08,0.14,9.73c2.72,2.65,7.08,2.59,9.73-0.14L64.94,66c2.65-2.74,2.59-7.11-0.15-9.76c-0.08-0.08-0.16-0.15-0.24-0.22L11.81,2.09c-2.65-2.73-7-2.79-9.73-0.14C-0.64,4.6-0.7,8.95,1.95,11.68l48.46,49.55L1.95,111.2z"
        fill="currentColor"
    />
</svg>`, paginaAtual + 1, paginaAtual === maxPage, false, true)
                );
            
                // >> Última página
                pageContainer.appendChild(
                    criarBotao(`
<svg
    width="15"
    height="15"
    viewBox="0 0 120.64 122.88"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
>
    <path
        d="M54.03,108.91c-1.55,1.63-2.31,3.74-2.28,5.85c0.03,2.11,0.84,4.2,2.44,5.79l0.12,0.12c1.58,1.5,3.6,2.23,5.61,2.2c2.01-0.03,4.01-0.82,5.55-2.37c17.66-17.66,35.13-35.61,52.68-53.4c0.07-0.05,0.13-0.1,0.19-0.16c1.55-1.63,2.31-3.76,2.28-5.87c-0.03-2.11-0.85-4.21-2.45-5.8l-0.27-0.26C100.43,37.47,82.98,19.87,65.46,2.36C63.93,0.82,61.93,0.03,59.92,0c-2.01-0.03-4.03,0.7-5.61,2.21l-0.15,0.15c-1.57,1.58-2.38,3.66-2.41,5.76c-0.03,2.1,0.73,4.22,2.28,5.85l47.22,47.27L54.03,108.91z
           M2.26,106.91c-1.54,1.62-2.29,3.73-2.26,5.83c0.03,2.11,0.84,4.2,2.44,5.79l0.12,0.12c1.57,1.5,3.6,2.23,5.61,2.21c2.01-0.03,4.02-0.82,5.55-2.37C31.01,101.2,48.87,84.2,66.39,67.12c0.07-0.05,0.14-0.11,0.21-0.17c1.55-1.63,2.31-3.76,2.28-5.87c-0.03-2.11-0.85-4.21-2.45-5.8C48.94,38.33,31.36,21.44,13.83,4.51l-0.12-0.13c-1.53-1.54-3.53-2.32-5.54-2.35C6.16,2,4.14,2.73,2.56,4.23L2.41,4.38C0.84,5.96,0.03,8.05,0,10.14c-0.03,2.1,0.73,4.22,2.28,5.85l47.18,45.24L2.26,106.91z"
        fill="currentColor"
    />
</svg>`, maxPage, paginaAtual === maxPage, false, true)
                );
            }
        } catch (e) {
            console.error("Erro ao carregar cards com filtros:", e);
        }
        
        // Salvamento de filtros
        let filtrosData = null;
        
        try {
            const response = await fetch("http://localhost:7777/api/filtros-imoveis");
            filtrosData = await response.json();
        } catch (e) {
            console.error("Erro ao carregar filtros:", e);
            return;
        }
            
        filtrosData.ufs.forEach(uf => {
            const opt = document.createElement("option");
            opt.value = uf.toLowerCase();
            opt.textContent = uf.toUpperCase();
            selectUf.appendChild(opt);
        });
        
        if (ufParam && ufParam !== "all") {
            selectUf.value = ufParam;
            
            const selecionarUf = (ufSelecionada) => {
                for (const item of filtrosData.ufs) {
                    if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const uf = selecionarUf(ufParam);
        
            selectCidade.innerHTML = `<option value="">Todas as cidades</option>`;
            selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
            selectCidade.disabled = true;
            selectBairro.disabled = true;
        
            if (!uf || !filtrosData.cidades[uf]) return;
        
            filtrosData.cidades[uf].forEach(c => {
                const opt = document.createElement("option");
                opt.value = c.toLowerCase();
                opt.textContent = c;
                selectCidade.appendChild(opt);
            });
            
            selectCidade.disabled = false;
        } else {
            selectUf.addEventListener("change", () => {
                const selecionarUf = (ufSelecionada) => {
                    for (const item of filtrosData.ufs) {
                        if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                            return item;
                        }
                    }
                    return null; // caso não encontre
                }
                
                const uf = selecionarUf(selectUf.value);
            
                selectCidade.innerHTML = `<option value="">Todas as cidades</option>`;
                selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
                selectCidade.disabled = true;
                selectBairro.disabled = true;
            
                if (!uf || !filtrosData.cidades[uf]) return;
            
                filtrosData.cidades[uf].forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.toLowerCase();
                    opt.textContent = c;
                    selectCidade.appendChild(opt);
                });
            
                selectCidade.disabled = false;
            });
        }
        
        filtrosData.modalidades_venda.forEach(m => {
            const opt = document.createElement("option");
            opt.value = m.toLowerCase();
            opt.textContent = m;
            selectModalidade.appendChild(opt);
        });
        
        if (modalidadeVendaParam && modalidadeVendaParam !== "all") {
            selectModalidade.value = modalidadeVendaParam;
        }
        
        if(cidadeParam && cidadeParam !== "all") {
            selectCidade.value = cidadeParam;
            const selecionarUf = (ufSelecionada) => {
                for (const item of filtrosData.ufs) {
                    if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const uf = selecionarUf(ufParam);
            
            const selecionarCidade = (cidadeSelecionada) => {
                for (const item of filtrosData.cidades[uf]) {
                    if (item.toLowerCase() === cidadeSelecionada.toLowerCase()) {
                        return item;
                    }
                }
                return null; // caso não encontre
            }
            
            const cidade = selecionarCidade(cidadeParam);
        
            selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
            selectBairro.disabled = true;
        
            if (!cidade || !filtrosData.bairros[cidade]) return;
        
            filtrosData.bairros[cidade].forEach(b => {
                const opt = document.createElement("option");
                opt.value = b.toLowerCase();
                opt.textContent = b;
                selectBairro.appendChild(opt);
            });
        
            selectBairro.disabled = false;
        } else {
            selectCidade.addEventListener("change", () => {
                const selecionarUf = (ufSelecionada) => {
                    for (const item of filtrosData.ufs) {
                        if (item.toLowerCase() === ufSelecionada.toLowerCase()) {
                            return item;
                        }
                    }
                    return null; // caso não encontre
                }
                
                const uf = selecionarUf(selectUf.value);
                
                const selecionarCidade = (cidadeSelecionada) => {
                    for (const item of filtrosData.cidades[uf]) {
                        if (item.toLowerCase() === cidadeSelecionada.toLowerCase()) {
                            return item;
                        }
                    }
                    return null; // caso não encontre
                }
                
                const cidade = selecionarCidade(selectCidade.value);
            
                selectBairro.innerHTML = `<option value="">Todos os bairros</option>`;
                selectBairro.disabled = true;
            
                if (!cidade || !filtrosData.bairros[cidade]) return;
            
                filtrosData.bairros[cidade].forEach(b => {
                    const opt = document.createElement("option");
                    opt.value = b.toLowerCase();
                    opt.textContent = b;
                    selectBairro.appendChild(opt);
                });
            
                selectBairro.disabled = false;
            });
        }
        
        if (bairroParam && bairroParam !== "all") {
            selectBairro.value = bairroParam;
        }
    }
    
    const infoLogin = document.getElementById("danger-login");
    
    const addFavorites = document.querySelectorAll(".btn-fav.add-fav");
    const removeFavorites = document.querySelectorAll(".btn-fav.remove-fav");
    
    addFavorites.forEach((item) => {
        item.addEventListener("click", () => {
            if (!item.classList.contains("active")) {
                infoLogin.classList.add("active");
                setTimeout(() => {
                    infoLogin.classList.remove("active")
                }, 5000);
            }
        });
    });
    
    removeFavorites.forEach((item) => {
        item.addEventListener("click", () => {
            if (!item.classList.contains("active")) {
                infoLogin.classList.add("active");
                setTimeout(() => {
                    infoLogin.classList.remove("active")
                }, 5000);
            }
        });
    });
    
    const openMenu = document.getElementById("menu-icon");
    const menuContainer = document.getElementById("menu-container");
    
    if (openMenu) {
        openMenu.addEventListener("click", () => {
            if (menuContainer) {
                document.body.classList.add("menu-open");
                menuContainer.classList.add("active");
            }
        });
    }
    
    const menuClose = document.getElementById("menu-close");
    
    if (menuClose) {
        menuClose.addEventListener("click", () => {
            if (menuContainer) {
                document.body.classList.remove("menu-open");
                menuContainer.classList.remove("active");
            }
        });
    }
    
    const btnClear = document.querySelector(".btn-clear");

    if (btnClear) {
        btnClear.addEventListener("click", () => {
            window.location.href = "http://localhost:7777/imoveis";
        });
    }

    /* =======================
       APLICAR FILTROS → URL
    ======================= */
    btnApply.addEventListener("click", () => {
        const uf = (selectUf.value || "all").toLowerCase();
        const cidade = (selectCidade.value || "all").toLowerCase();
        const bairro = (selectBairro.value || "all").toLowerCase();
    
        const precoMin = priceMin.value && priceMin.value > 0 ? priceMin.value : "0";
        const precoMax = priceMax.value && priceMax.value > 0 ? priceMax.value : "0";
    
        const percentMinVal = percentMin.value && percentMin.value > 0 ? percentMin.value : "0";
        const percentMaxVal = percentMax.value && percentMax.value > 0 ? percentMax.value : "0";
    
        const modalidade = (selectModalidade.value || "all").toLowerCase();
    
        const url = `/imoveis/${uf}/${cidade}/${bairro}/${precoMin}/${precoMax}/${percentMinVal}/${percentMaxVal}/${modalidade}/1`;
    
        window.location.href = url;
    });
});