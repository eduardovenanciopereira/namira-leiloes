function renderCardsAutomatico(listaVeiculosPorIndice) {
    const containers = document.querySelectorAll(".cards-container");
    if (!containers.length) return;

    const infoQuantity = document.getElementById("info-quantity");
    if (!infoQuantity) return;

    let totalVeiculosRenderizados = 0;

    containers.forEach((container, index) => {
        container.innerHTML = "";

        const listaVeiculos = listaVeiculosPorIndice[index];
        if (!listaVeiculos || !listaVeiculos.length) return;

        listaVeiculos.forEach(veiculo => {
            totalVeiculosRenderizados++;

            const card = document.createElement("div");
            card.className = "card";
            card.style.cursor = "pointer";
            card.addEventListener("click", () => {
                window.location.href = `/veiculo/${veiculo.slug}-${veiculo.id}`;
            });

            const img = document.createElement("img");
            
            if (veiculo.imagens && veiculo.imagens.length > 0) {
            
                const originalUrl = veiculo.imagens[0];

                if (originalUrl && originalUrl.indexOf("mgl.com.br") !== -1) {

                    const imageId = originalUrl.replace("https://www.mgl.com.br/imagens/1300x1300/", "");

                    img.src = `http://localhost:7777/api/imagem-mgl/${imageId}`;
            
                } else {
                    img.src = originalUrl;
                }
            
            } else {
                img.src = "";
            }
            
            img.alt = veiculo.titulo || "Veículo";
            card.appendChild(img);
            
            img.alt = veiculo.titulo || "Veículo";
            card.appendChild(img);

            const body = document.createElement("div");
            body.className = "card-body";

            const badges = document.createElement("div");
            badges.className = "card-badges";

            const badgeStatus = document.createElement("span");
            badgeStatus.className = `badge badge-status ${veiculo.status.toLowerCase().replace(/\s/g, "-")}`;
            badgeStatus.textContent = veiculo.status;

            const badgeCategoria = document.createElement("span");
            badgeCategoria.className = "badge badge-categoria";
            badgeCategoria.textContent = veiculo.categoria;

            badges.appendChild(badgeStatus);
            badges.appendChild(badgeCategoria);
            body.appendChild(badges);

            const title = document.createElement("h3");
            title.className = "card-title";
            title.textContent = veiculo.titulo;
            body.appendChild(title);

            const location = document.createElement("p");
            location.className = "card-location";
            location.textContent = `${veiculo.cidade}, ${veiculo.uf}`;
            body.appendChild(location);

            const valorExibido = veiculo.valor_atual > 0 ? veiculo.valor_atual : veiculo.valor_inicial;

            const price = document.createElement("p");
            price.className = "card-price";
            price.textContent = `R$ ${Number(valorExibido).toLocaleString("pt-BR")}`;
            body.appendChild(price);

            const desc = document.createElement("p");
            desc.className = "card-desc";
            desc.textContent = `Ano ${veiculo.ano_fabricacao}/${veiculo.ano_modelo}`;
            body.appendChild(desc);

            const actions = document.createElement("div");
            actions.className = "card-actions";

            const btnFavContainer = document.createElement("div");
            btnFavContainer.className = "btn-fav-container";

            const btnAddFav = document.createElement("button");
            btnAddFav.className = "btn-fav add-fav";
            btnAddFav.dataset.idExterno = veiculo.id_externo;
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
            btnRemoveFav.dataset.idExterno = veiculo.id_externo;
            btnRemoveFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110.61 122.88" width="24" height="24" fill="currentColor">
    <path d="M39.27,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Zm63.6-19.86L98,103a22.29,22.29,0,0,1-6.33,14.1,19.41,19.41,0,0,1-13.88,5.78h-45a19.4,19.4,0,0,1-13.86-5.78l0,0A22.31,22.31,0,0,1,12.59,103L7.74,38.78H0V25c0-3.32,1.63-4.58,4.84-4.58H27.58V10.79A10.82,10.82,0,0,1,38.37,0H72.24A10.82,10.82,0,0,1,83,10.79v9.62h23.35a6.19,6.19,0,0,1,1,.06A3.86,3.86,0,0,1,110.59,24c0,.2,0,.38,0,.57V38.78Zm-9.5.17H17.24L22,102.3a12.82,12.82,0,0,0,3.57,8.1l0,0a10,10,0,0,0,7.19,3h45a10.06,10.06,0,0,0,7.19-3,12.8,12.8,0,0,0,3.59-8.1L93.37,39ZM71,20.41V12.05H39.64v8.36ZM61.87,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Z"/>
</svg>`;
            btnRemoveFav.addEventListener("click", e => e.stopPropagation());

            btnFavContainer.appendChild(btnAddFav);
            btnFavContainer.appendChild(btnRemoveFav);

            actions.appendChild(btnFavContainer);

            body.appendChild(actions);
            card.appendChild(body);
            container.appendChild(card);
        });
    });

    infoQuantity.textContent = `${totalVeiculosRenderizados} disponíveis`;
}

function renderCardsComFiltros(veiculos, paginas) {
    const cardsContent = document.getElementById("cards-content");
    if (!cardsContent) return;

    cardsContent.innerHTML = "";

    const infoQuantity = document.getElementById("info-quantity");
    if (!infoQuantity) return;

    infoQuantity.textContent = `${veiculos.length * paginas} disponíveis`;

    if (!veiculos || !veiculos.length) return;

    const MAX_ROWS = 7;
    const CARDS_PER_ROW = 8;

    const totalRows = Math.min(
        Math.ceil(veiculos.length / CARDS_PER_ROW),
        MAX_ROWS
    );

    let index = 0;

    for (let r = 0; r < totalRows; r++) {
        const row = document.createElement("div");
        row.className = "cards-row";

        const container = document.createElement("div");
        container.className = "cards-container";

        for (let c = 0; c < CARDS_PER_ROW && index < veiculos.length; c++, index++) {
            const veiculo = veiculos[index];

            const card = document.createElement("div");
            card.className = "card";
            card.style.cursor = "pointer";
            card.addEventListener("click", () => {
                window.location.href = `/veiculo/${veiculo.slug}-${veiculo.id}`;
            });

            const img = document.createElement("img");

            if (veiculo.imagens && veiculo.imagens.length > 0) {
                const originalUrl = veiculo.imagens[0];

                if (originalUrl && originalUrl.indexOf("mgl.com.br") !== -1) {
                    const imageId = originalUrl.replace(
                        "https://www.mgl.com.br/imagens/1300x1300/",
                        ""
                    );
                    img.src = `http://localhost:7777/api/imagem-mgl/${imageId}`;
                } else {
                    img.src = originalUrl;
                }
            } else {
                img.src = "";
            }

            img.alt = veiculo.titulo || "Veículo";
            card.appendChild(img);

            const body = document.createElement("div");
            body.className = "card-body";

            const badges = document.createElement("div");
            badges.className = "card-badges";

            const badgeStatus = document.createElement("span");
            badgeStatus.className = `badge badge-status ${veiculo.status.toLowerCase().replace(/\s/g, "-")}`;
            badgeStatus.textContent = veiculo.status;

            const badgeCategoria = document.createElement("span");
            badgeCategoria.className = "badge badge-categoria";
            badgeCategoria.textContent = veiculo.categoria;

            badges.appendChild(badgeStatus);
            badges.appendChild(badgeCategoria);
            body.appendChild(badges);

            const title = document.createElement("h3");
            title.className = "card-title";
            title.textContent = veiculo.titulo;
            body.appendChild(title);

            const location = document.createElement("p");
            location.className = "card-location";
            location.textContent = `${veiculo.cidade}, ${veiculo.uf}`;
            body.appendChild(location);

            const valorExibido = veiculo.valor_atual > 0 ? veiculo.valor_atual : veiculo.valor_inicial;

            const price = document.createElement("p");
            price.className = "card-price";
            price.textContent = `R$ ${Number(valorExibido).toLocaleString("pt-BR")}`;
            body.appendChild(price);

            const desc = document.createElement("p");
            desc.className = "card-desc";
            desc.textContent = `Ano ${veiculo.ano_fabricacao}/${veiculo.ano_modelo}`;
            body.appendChild(desc);

            const actions = document.createElement("div");
            actions.className = "card-actions";

            const btnFavContainer = document.createElement("div");
            btnFavContainer.className = "btn-fav-container";

            const btnAddFav = document.createElement("button");
            btnAddFav.dataset.idExterno = veiculo.id_externo;
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
            btnRemoveFav.dataset.idExterno = veiculo.id_externo;
            btnRemoveFav.innerHTML = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110.61 122.88" width="24" height="24" fill="currentColor">
    <path d="M39.27,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Zm63.6-19.86L98,103a22.29,22.29,0,0,1-6.33,14.1,19.41,19.41,0,0,1-13.88,5.78h-45a19.4,19.4,0,0,1-13.86-5.78l0,0A22.31,22.31,0,0,1,12.59,103L7.74,38.78H0V25c0-3.32,1.63-4.58,4.84-4.58H27.58V10.79A10.82,10.82,0,0,1,38.37,0H72.24A10.82,10.82,0,0,1,83,10.79v9.62h23.35a6.19,6.19,0,0,1,1,.06A3.86,3.86,0,0,1,110.59,24c0,.2,0,.38,0,.57V38.78Zm-9.5.17H17.24L22,102.3a12.82,12.82,0,0,0,3.57,8.1l0,0a10,10,0,0,0,7.19,3h45a10.06,10.06,0,0,0,7.19-3,12.8,12.8,0,0,0,3.59-8.1L93.37,39ZM71,20.41V12.05H39.64v8.36ZM61.87,58.64a4.74,4.74,0,1,1,9.47,0V93.72a4.74,4.74,0,1,1-9.47,0V58.64Z"/>
</svg>`;
            btnRemoveFav.addEventListener("click", e => e.stopPropagation());

            btnFavContainer.appendChild(btnAddFav);
            btnFavContainer.appendChild(btnRemoveFav);

            actions.appendChild(btnFavContainer);
            body.appendChild(actions);

            card.appendChild(body);
            container.appendChild(card);

        }

        row.appendChild(container);
        cardsContent.appendChild(row);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    const responseLogged = await fetch("http://localhost:7777/api/logado", {
    method: "GET",
    headers: {
        "Content-Type": "application/json"
    },
    credentials: "include"
    });
    
    if (responseLogged.ok) {
        const resultLogged = await responseLogged.json();
    
        if (resultLogged.is_logged) {
            await fetch("http://localhost:7777/api/gerar-acesso", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "include"
            });
            const responseDetails = await fetch("http://localhost:7777/api/detalhes", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                },
                credentials: "include"
            });
            
            if (responseDetails.ok) {
                const resultDetails = await responseDetails.json();
                console.log(resultDetails);
                if (resultDetails && resultDetails.is_admin) {
                    const quantityFavorites = resultDetails ? resultDetails.quantity_favorites : "0";
                    const quantityMessages = resultDetails ? resultDetails.quantity_messages : "0";
                    const quantityContacts = resultDetails ? resultDetails.quantity_contacts : "0";
                    const quantityBlogPosts = resultDetails ? resultDetails.quantity_blog_posts : "0";
                    
                    const containerAccountDetails = document.getElementById("container-account-details");

                    containerAccountDetails.innerHTML = "";
                    
                    const contentAccountDetails = document.createElement("div");
                    contentAccountDetails.className = "content-acc-details";
                    
                    const ulPages = document.createElement("ul");
                    ulPages.className = "pages-list";
                    
                    const pages = [
                        {
                            href: "/favoritos",
                            text: `Favoritos (${quantityFavorites})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 107.41"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M60.83,17.19C68.84,8.84,74.45,1.62,86.79,0.21c23.17-2.66,44.48,21.06,32.78,44.41c-3.33,6.65-10.11,14.56-17.61,22.32c-8.23,8.52-17.34,16.87-23.72,23.2l-17.4,17.26L46.46,93.56C29.16,76.9,0.95,55.93,0.02,29.95C-0.63,11.75,13.73,0.09,30.25,0.3C45.01,0.5,51.22,7.84,60.83,17.19z"/></svg>`
                        },
                        {
                            href: "/mensagens",
                            text: `Mensagens (${quantityMessages})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.883 104.293"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M4.878,104.293h113.125c2.682,0,4.879-2.211,4.879-4.914l-0.115-38.84c-0.006-2.645-0.387-4.012-1.338-6.492L102.379,4.38C101.516,2.132,100.408,0,97.998,0H25.729c-2.41,0-3.488,2.144-4.38,4.38L1.22,54.865c-0.966,2.424-1.063,3.809-1.072,6.438L0,99.379C0,102.082,2.198,104.293,4.878,104.293z M107.496,57.518H82.111l-7.758,15.617H48.633l-7.862-15.617H15.195l17.922-41.943h57.184L107.496,57.518z"/></svg>`
                        },
                        {
                            href: "/solicitacoes",
                            text: `Solicitações (${quantityContacts})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 513.07 515.83"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M65.79 0h301.26c36.19 0 65.79 29.63 65.79 65.79v384.26c0 36.15-29.63 65.78-65.79 65.78H65.79C29.63 515.83 0 486.23 0 450.05V65.79C0 29.6 29.6 0 65.79 0zm386.16 337.23h46.62c1.76 0 3.42.29 5.06.87 2.13.75 3.86 1.84 5.29 3.27l-.01.01c1.48 1.46 2.58 3.23 3.28 5.25l.01.04c.58 1.67.88 3.38.88 5.06v67.94c0 1.18-.16 2.36-.48 3.56-.08.32-.17.65-.28.99-.66 1.97-1.72 3.68-3.14 5.11a13.582 13.582 0 01-3.5 2.55c-1.84.93-3.75 1.39-5.72 1.39h-48v-96.04zm0-254.67h46.63c2.17 0 4.2.47 6.15 1.38 2.21 1.04 3.93 2.47 5.29 4.29l-.03.02c1.05 1.41 1.84 2.88 2.35 4.49.52 1.61.78 3.19.78 4.79v68.04c0 1.47-.27 2.94-.82 4.37-.2.58-.46 1.15-.76 1.71-.82 1.5-1.96 2.87-3.42 4-.75.61-1.55 1.12-2.39 1.55l-.04.01c-1.85.93-3.74 1.4-5.67 1.4h-48.01V82.56zm0 126.07h46.62c2.2 0 4.24.46 6.2 1.38 2.19 1.03 3.9 2.45 5.26 4.26l-.03.02c.92 1.24 1.63 2.51 2.12 3.82.64 1.7.96 3.39.96 5.07v68.44c0 1.97-.46 3.87-1.4 5.72a13.33 13.33 0 01-2.51 3.49c-1.1 1.09-2.27 1.95-3.51 2.57-1.85.93-3.73 1.39-5.65 1.39h-48v-96.13zm-291.62 43.29c-4.91-7.72-14.06-18.32-14.06-27.5.22-6.08 4.16-11.41 9.91-13.41-.45-7.72-.76-15.59-.76-23.38 0-4.6 0-9.24.26-13.8.24-2.89.76-5.75 1.55-8.55a49.153 49.153 0 0121.95-27.88 64.81 64.81 0 0111.91-5.7c7.6-2.73 3.84-15.43 12.11-15.6 19.32-.5 51.08 17.18 63.47 30.6a49.034 49.034 0 0112.66 31.78l-.79 33.84c3.71.77 6.78 3.38 8.13 6.93 2.64 10.7-8.44 23.97-13.59 32.48-4.76 7.84-22.92 33.19-22.92 33.36 2.57 38.29 90.7 8.29 90.7 89.18H91.98c0-79.02 92.03-53.54 91.08-89.25 0-.5-20.87-30.02-22.73-33z"/></svg>`
                        },
                        {
                            href: "/gerenciar-blog",
                            text: `Gerenciar blog (${quantityBlogPosts})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 117.74 122.88"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M94.62,2c-1.46-1.36-3.14-2.09-5.02-1.99c-1.88,0-3.56,0.73-4.92,2.2L73.59,13.72l31.07,30.03l11.19-11.72c1.36-1.36,1.88-3.14,1.88-5.02s-0.73-3.66-2.09-4.92L94.62,2z M41.44,109.58c-4.08,1.36-8.26,2.62-12.35,3.98c-4.08,1.36-8.16,2.72-12.35,4.08c-9.73,3.14-15.07,4.92-16.22,5.23c-1.15,0.31-0.42-4.18,1.99-13.6l7.74-29.61l0.64-0.66l30.56,30.56L41.44,109.58z M22.2,67.25l42.99-44.82l31.07,29.92L52.75,97.8L22.2,67.25z"/></svg>`
                        },
                        {
                            href: "/enviar-mensagem",
                            text: "Enviar mensagem",
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.56 122.88"><path fill="currentColor" fill-rule="evenodd" d="M2.33,44.58,117.33.37a3.63,3.63,0,0,1,5,4.56l-44,115.61a3.63,3.63,0,0,1-6.67.28L53.93,84.14,89.12,33.77,38.85,68.86,2.06,51.24a3.63,3.63,0,0,1,.27-6.66Z"/></svg>`
                        },
                        {
                            href: "/excluir-conta",
                            text: "Excluir conta",
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 109.484 122.88"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M2.347,9.633h38.297V3.76c0-2.068,1.689-3.76,3.76-3.76h21.144c2.07,0,3.76,1.691,3.76,3.76v5.874h37.83c1.293,0,2.347,1.057,2.347,2.349v11.514H0V11.982C0,10.69,1.055,9.633,2.347,9.633z M8.69,29.605h92.921c1.937,0,3.696,1.599,3.521,3.524l-7.864,86.229c-0.174,1.926-1.59,3.521-3.523,3.521h-77.3c-1.934,0-3.352-1.592-3.524-3.521L5.166,33.129C4.994,31.197,6.751,29.605,8.69,29.605z M69.077,42.998h9.866v65.314h-9.866V42.998z M30.072,42.998h9.867v65.314h-9.867V42.998z M49.572,42.998h9.869v65.314h-9.869V42.998z"/></svg>`
                        }
                    ];
                    
                    pages.forEach(page => {
                        const liPage = document.createElement("li");
                        liPage.className = "page-option";
                    
                        liPage.innerHTML = `
                            ${page.svg}
                            <a href="${page.href}">${page.text}</a>
                        `;
                    
                        ulPages.appendChild(liPage);
                    });
                    
                    contentAccountDetails.appendChild(ulPages);
                    containerAccountDetails.appendChild(contentAccountDetails);
                } else {
                    const quantityFavorites = resultDetails ? resultDetails.quantity_favorites : "0";
                    const quantityMessages = resultDetails ? resultDetails.quantity_messages : "0";
                    
                    const containerAccountDetails = document.getElementById("container-account-details");

                    containerAccountDetails.innerHTML = "";
                    
                    const contentAccountDetails = document.createElement("div");
                    contentAccountDetails.className = "content-acc-details";
                    
                    const ulPages = document.createElement("ul");
                    ulPages.className = "pages-list";
                    
                    const pages = [
                        {
                            href: "/favoritos",
                            text: `Favoritos (${quantityFavorites})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 107.41"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M60.83,17.19C68.84,8.84,74.45,1.62,86.79,0.21c23.17-2.66,44.48,21.06,32.78,44.41c-3.33,6.65-10.11,14.56-17.61,22.32c-8.23,8.52-17.34,16.87-23.72,23.2l-17.4,17.26L46.46,93.56C29.16,76.9,0.95,55.93,0.02,29.95C-0.63,11.75,13.73,0.09,30.25,0.3C45.01,0.5,51.22,7.84,60.83,17.19z"/></svg>`
                        },
                        {
                            href: "/mensagens",
                            text: `Mensagens (${quantityMessages})`,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.883 104.293"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M4.878,104.293h113.125c2.682,0,4.879-2.211,4.879-4.914l-0.115-38.84c-0.006-2.645-0.387-4.012-1.338-6.492L102.379,4.38C101.516,2.132,100.408,0,97.998,0H25.729c-2.41,0-3.488,2.144-4.38,4.38L1.22,54.865c-0.966,2.424-1.063,3.809-1.072,6.438L0,99.379C0,102.082,2.198,104.293,4.878,104.293z M107.496,57.518H82.111l-7.758,15.617H48.633l-7.862-15.617H15.195l17.922-41.943h57.184L107.496,57.518z"/></svg>`
                        },
                        {
                            href: "/excluir-conta",
                            text: "Excluir conta",
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 109.484 122.88"><path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M2.347,9.633h38.297V3.76c0-2.068,1.689-3.76,3.76-3.76h21.144c2.07,0,3.76,1.691,3.76,3.76v5.874h37.83c1.293,0,2.347,1.057,2.347,2.349v11.514H0V11.982C0,10.69,1.055,9.633,2.347,9.633z M8.69,29.605h92.921c1.937,0,3.696,1.599,3.521,3.524l-7.864,86.229c-0.174,1.926-1.59,3.521-3.523,3.521h-77.3c-1.934,0-3.352-1.592-3.524-3.521L5.166,33.129C4.994,31.197,6.751,29.605,8.69,29.605z M69.077,42.998h9.866v65.314h-9.866V42.998z M30.072,42.998h9.867v65.314h-9.867V42.998z M49.572,42.998h9.869v65.314h-9.869V42.998z"/></svg>`
                        }
                    ];
                    
                    pages.forEach(page => {
                        const liPage = document.createElement("li");
                        liPage.className = "page-option";
                    
                        liPage.innerHTML = `
                            ${page.svg}
                            <a href="${page.href}">${page.text}</a>
                        `;
                    
                        ulPages.appendChild(liPage);
                    });
                    
                    contentAccountDetails.appendChild(ulPages);
                    containerAccountDetails.appendChild(contentAccountDetails);
                }
            }
            
            const btnEnter = document.getElementById("btn-login");
            const btnLogged = document.getElementById("btn-logged");
            const containerDetailsAccount = document.getElementById("container-account-details");
            
            if (btnEnter) {
                btnEnter.remove();
                if (btnLogged) {
                    btnLogged.classList.add("active");
                }
            }
            
            btnLogged.addEventListener("click", () => {
                containerDetailsAccount.classList.toggle("active");
            });
        }
    } else {
        localStorage.setItem("lastPage", window.location.pathname);
    }
    
    const btnFilter = document.getElementById("btn-filter");
    const filtersContainer = document.getElementById("filters-container");

    if (btnFilter && filtersContainer) {
        btnFilter.addEventListener("click", function () {
            this.classList.toggle("active");
            filtersContainer.classList.toggle("active");
        });
    }

    const selectCategoria = document.getElementById("filter-categoria");
    const selectUf = document.getElementById("filter-uf");
    const selectCidade = document.getElementById("filter-cidade");
    const selectStatus = document.getElementById("filter-status");
    const selectAnoModelo = document.getElementById("filter-ano-modelo");

    const routeParams = window.ROUTE ? window.ROUTE.params : null;
    
    const pageContainer = document.getElementById("page-container");
    
    if (!routeParams) {
        if (pageContainer) {
            pageContainer.remove();
        }
        const urls = [
            "http://localhost:7777/api/veiculos/carros/all/all/all/20000/100000/0/all/1/8",
            "http://localhost:7777/api/veiculos/carros/all/all/all/60000/120000/0/all/5/8",
            "http://localhost:7777/api/veiculos/motos/all/all/all/1000/12000/0/all/5/8",
            "http://localhost:7777/api/veiculos/motos/all/all/all/1000/12000/0/all/6/8",
            "http://localhost:7777/api/veiculos/motos/all/all/all/1000/12000/0/all/7/8",
            "http://localhost:7777/api/veiculos/caminhonetes/all/all/all/60000/120000/0/all/1/8",
            "http://localhost:7777/api/veiculos/caminhonetes/all/all/all/100000/200000/0/all/5/8",
        ];

        const responseDestaques = await Promise.all(urls.map(url => fetch(url)));
        const resultDestaques = await Promise.all(responseDestaques.map(res => res.json()));
        const listaVeiculosPorIndice = resultDestaques.map(r => r.data);
        renderCardsAutomatico(listaVeiculosPorIndice);

        let filtrosData = null;

        const responseFiltros = await fetch("http://localhost:7777/api/filtros-veiculos");
        filtrosData = await responseFiltros.json();
    
        const resetSelect = (select, text) => {
            select.innerHTML = `<option value="">${text}</option>`;
            select.disabled = true;
        };

        const popularSelect = (select, lista) => {
            if (!lista || lista.length === 0) return;
            lista.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item;
                opt.textContent = item;
                select.appendChild(opt);
            });
            select.disabled = false;
        };

        resetSelect(selectCategoria, "Selecione uma categoria");
        resetSelect(selectUf, "Selecione um estado");
        resetSelect(selectCidade, "Selecione uma cidade");
        resetSelect(selectStatus, "Selecione um status");
        resetSelect(selectAnoModelo, "Selecione o ano");
    
        if (filtrosData.categorias && Array.isArray(filtrosData.categorias)) {
            popularSelect(selectCategoria, filtrosData.categorias);
        }

        selectCategoria.addEventListener("change", () => {
            const categoria = selectCategoria.value;

            resetSelect(selectUf, "Selecione um estado");
            resetSelect(selectCidade, "Selecione uma cidade");
            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!categoria) return;

            const listaUfs = filtrosData.ufs ? filtrosData.ufs[categoria] : [];
            popularSelect(selectUf, listaUfs);
        });
    
        selectUf.addEventListener("change", () => {
            const uf = selectUf.value;

            resetSelect(selectCidade, "Selecione uma cidade");
            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!uf) return;

            const listaCidades = filtrosData.cidades ? filtrosData.cidades[uf] : [];
            popularSelect(selectCidade, listaCidades);

            const listaStatus = filtrosData.status_ufs ? filtrosData.status_ufs[uf] : [];
            popularSelect(selectStatus, listaStatus);
        });

        selectCidade.addEventListener("change", () => {
            const uf = selectUf.value;
            const cidade = selectCidade.value;

            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!uf) return;

            if (cidade) {
                const listaStatusCidade = (filtrosData.status_cidades && filtrosData.status_cidades[uf]) ? filtrosData.status_cidades[uf][cidade] : [];
                popularSelect(selectStatus, listaStatusCidade);
            } else {
                const listaStatusUf = filtrosData.status_ufs ? filtrosData.status_ufs[uf] : [];
                popularSelect(selectStatus, listaStatusUf);
            }
        });

        selectStatus.addEventListener("change", () => {
            const uf = selectUf.value;
            const cidade = selectCidade.value;
            const status = selectStatus.value;
    
            resetSelect(selectAnoModelo, "Selecione o ano");
    
            if (!uf || !status) return;
    
            let listaAnos = [];
    
            if (cidade) {
                if (filtrosData.anos_cidades && filtrosData.anos_cidades[uf] && filtrosData.anos_cidades[uf][cidade]) {
                    listaAnos = filtrosData.anos_cidades[uf][cidade][status] || [];
                }
            } else {
                if (filtrosData.anos_ufs && filtrosData.anos_ufs[uf]) {

                    listaAnos = filtrosData.anos_ufs[uf][status] || [];
                }
            }
    
            popularSelect(selectAnoModelo, listaAnos);
        });
    } else {
        const categoriaParam = (routeParams[0] ?? "all").toLowerCase().replace(/-/g, " ");
        const ufParam = (routeParams[1] ?? "all").toLowerCase();
        const cidadeParam = (routeParams[2] ? decodeURIComponent(routeParams[2]) : "all").toLowerCase().replace(/-/g, " ");
        const statusParam = (routeParams[3] ? decodeURIComponent(routeParams[3]) : "all").toLowerCase().replace(/-/g, " ");
        const minValorParam = routeParams[4] ?? "0";
        const maxValorParam = routeParams[5] ?? "0";
        const anoModeloParam = routeParams[6] ?? "0";
        const buscaTituloParam = (routeParams[7] ? decodeURIComponent(routeParams[7]) : "all").toLowerCase().replace(/-/g, " ");
        const paginaAtualParam = routeParams[8] ?? "1";
        const quantidadeParam = 56;

        const url = `http://localhost:7777/api/veiculos/${categoriaParam}/${ufParam}/${cidadeParam}/${statusParam}/${minValorParam}/${maxValorParam}/${anoModeloParam}/${buscaTituloParam}/${paginaAtualParam}/${quantidadeParam}`;

        const textTitle = document.getElementById("text-title");
        textTitle.textContent = "Resultados encontrados";

        const responsePesquisa = await fetch(url);
        const resultPesquisa = await responsePesquisa.json();

        renderCardsComFiltros(resultPesquisa.data, resultPesquisa.paginas);

        const maxPage = Number(resultPesquisa.paginas);
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
                    window.location.href = `/veiculos/${categoriaParam}/${ufParam}/${cidadeParam}/${statusParam}/${minValorParam}/${maxValorParam}/${anoModeloParam}/${buscaTituloParam}/${page}`;
                };
                return btn;
            };

            pageContainer.appendChild(criarBotao('<svg width="15" height="15" viewBox="0 0 120.64 122.88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M66.6,108.91c1.55,1.63,2.31,3.74,2.28,5.85c-0.03,2.11-0.84,4.2-2.44,5.79l-0.12,0.12c-1.58,1.5-3.6,2.23-5.61,2.2c-2.01-0.03-4.02-0.82-5.55-2.37C37.5,102.85,20.03,84.9,2.48,67.11c-0.07-0.05-0.13-0.1-0.19-0.16C0.73,65.32-0.03,63.19,0,61.08c0.03-2.11,0.85-4.21,2.45-5.8l0.27-0.26C20.21,37.47,37.65,19.87,55.17,2.36C56.71,0.82,58.7,0.03,60.71,0c2.01-0.03,4.03,0.7,5.61,2.21l0.15,0.15c1.57,1.58,2.38,3.66,2.41,5.76c0.03,2.1-0.73,4.22-2.28,5.85L19.38,61.23L66.6,108.91zM118.37,106.91c1.54,1.62,2.29,3.73,2.26,5.83c-0.03,2.11-0.84,4.2-2.44,5.79l-0.12,0.12c-1.57,1.5-3.6,2.23-5.61,2.21c-2.01-0.03-4.02-0.82-5.55-2.37C89.63,101.2,71.76,84.2,54.24,67.12c-0.07-0.05-0.14-0.11-0.21-0.17c-1.55-1.63-2.31-3.76-2.28-5.87c0.03-2.11,0.85-4.21,2.45-5.8C71.7,38.33,89.27,21.44,106.8,4.51l0.12-0.13c1.53-1.54,3.53-2.32,5.54-2.35c2.01-0.03,4.03,0.7,5.61,2.21l0.15,0.15c1.57,1.58,2.38,3.66,2.41,5.76c0.03,2.1-0.73,4.22-2.28,5.85L71.17,61.23L118.37,106.91z" fill="currentColor"/></svg>', 1, paginaAtual === 1, false, true));

            pageContainer.appendChild(criarBotao('<svg width="15" height="15" viewBox="0 0 66.91 122.88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M64.96,111.2c2.65,2.73,2.59,7.08-0.13,9.73c-2.73,2.65-7.08,2.59-9.73-0.14L1.97,66.01c-2.65-2.74-2.59-7.1,0.15-9.76c0.08-0.08,0.16-0.15,0.24-0.22L55.1,2.09c2.65-2.73,7-2.79,9.73-0.14c2.73,2.65,2.78,7.01,0.13,9.73L16.5,61.23L64.96,111.2z" fill="currentColor"/></svg>', paginaAtual - 1, paginaAtual === 1, false, true));

            const maxSide = 1;

            const start = Math.max(1, paginaAtual - maxSide);
            const end = Math.min(maxPage, paginaAtual + maxSide);

            for (let i = start; i <= end; i++) {
                pageContainer.appendChild(criarBotao(i, i, false, i === paginaAtual));
            }

            pageContainer.appendChild(criarBotao('<svg width="15" height="15" viewBox="0 0 66.91 122.88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M1.95,111.2c-2.65,2.72-2.59,7.08,0.14,9.73c2.72,2.65,7.08,2.59,9.73-0.14L64.94,66c2.65-2.74,2.59-7.11-0.15-9.76c-0.08-0.08-0.16-0.15-0.24-0.22L11.81,2.09c-2.65-2.73-7-2.79-9.73-0.14C-0.64,4.6-0.7,8.95,1.95,11.68l48.46,49.55L1.95,111.2z" fill="currentColor"/></svg>', paginaAtual + 1, paginaAtual === maxPage, false, true));

            pageContainer.appendChild(criarBotao('<svg width="15" height="15" viewBox="0 0 120.64 122.88" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="M54.03,108.91c-1.55,1.63-2.31,3.74-2.28,5.85c0.03,2.11,0.84,4.2,2.44,5.79l0.12,0.12c1.58,1.5,3.6,2.23,5.61,2.2c2.01-0.03,4.01-0.82,5.55-2.37c17.66-17.66,35.13-35.61,52.68-53.4c0.07-0.05,0.13-0.1,0.19-0.16c1.55-1.63,2.31-3.76,2.28-5.87c-0.03-2.11-0.85-4.21-2.45-5.8l-0.27-0.26C100.43,37.47,82.98,19.87,65.46,2.36C63.93,0.82,61.93,0.03,59.92,0c-2.01-0.03-4.03,0.7-5.61,2.21l-0.15,0.15c-1.57,1.58-2.38,3.66-2.41,5.76c-0.03,2.1,0.73,4.22,2.28,5.85l47.22,47.27L54.03,108.91zM2.26,106.91c-1.54,1.62-2.29,3.73-2.26,5.83c0.03,2.11,0.84,4.2,2.44,5.79l0.12,0.12c1.57,1.5,3.6,2.23,5.61,2.21c2.01-0.03,4.02-0.82,5.55-2.37C31.01,101.2,48.87,84.2,66.39,67.12c0.07-0.05,0.14-0.11,0.21-0.17c1.55-1.63,2.31-3.76,2.28-5.87c-0.03-2.11-0.85-4.21-2.45-5.8C48.94,38.33,31.36,21.44,13.83,4.51l-0.12-0.13c-1.53-1.54-3.53-2.32-5.54-2.35C6.16,2,4.14,2.73,2.56,4.23L2.41,4.38C0.84,5.96,0.03,8.05,0,10.14c-0.03,2.1,0.73,4.22,2.28,5.85l47.18,45.24L2.26,106.91z" fill="currentColor"/></svg>', maxPage, paginaAtual === maxPage, false, true));
        }

        let filtrosData = null;

        const responseFiltros = await fetch("http://localhost:7777/api/filtros-veiculos");
        filtrosData = await responseFiltros.json();

        const resetSelect = (select, text) => {
            select.innerHTML = `<option value="">${text}</option>`;
            select.disabled = true;
        };

        const popularSelect = (select, lista) => {
            if (!lista || lista.length === 0) return;

            lista.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item;
                opt.textContent = item;
                select.appendChild(opt);
            });
            select.disabled = false;
        };

        const encontrarItem = (lista, parametro) => {
            if (!lista || !parametro || parametro === "all" || parametro === "0") return null;
            return lista.find(item => String(item).toLowerCase() === String(parametro)) || null;
        };

        resetSelect(selectCategoria, "Selecione uma categoria");
        resetSelect(selectUf, "Selecione um estado");
        resetSelect(selectCidade, "Selecione uma cidade");
        resetSelect(selectStatus, "Selecione um status");
        resetSelect(selectAnoModelo, "Selecione o ano");

        if (filtrosData.categorias && Array.isArray(filtrosData.categorias)) {
            popularSelect(selectCategoria, filtrosData.categorias);
        }

        selectCategoria.addEventListener("change", () => {
            const categoria = selectCategoria.value;
            resetSelect(selectUf, "Selecione um estado");
            resetSelect(selectCidade, "Selecione uma cidade");
            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!categoria) return;

            const listaUfs = filtrosData.ufs ? filtrosData.ufs[categoria] : [];
            popularSelect(selectUf, listaUfs);
        });

        selectUf.addEventListener("change", () => {
            const uf = selectUf.value;
            resetSelect(selectCidade, "Selecione uma cidade");
            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!uf) return;

            const listaCidades = filtrosData.cidades ? filtrosData.cidades[uf] : [];
            popularSelect(selectCidade, listaCidades);

            const listaStatus = filtrosData.status_ufs ? filtrosData.status_ufs[uf] : [];
            popularSelect(selectStatus, listaStatus);
        });

        selectCidade.addEventListener("change", () => {
            const uf = selectUf.value;
            const cidade = selectCidade.value;
        
            resetSelect(selectStatus, "Selecione um status");
            resetSelect(selectAnoModelo, "Selecione o ano");
        
            if (!uf) return;
        
            if (cidade) {
                const listaStatusCidade = (filtrosData.status_cidades && filtrosData.status_cidades[uf]) ? filtrosData.status_cidades[uf][cidade] : [];
                popularSelect(selectStatus, listaStatusCidade);
            } else {
                const listaStatusUf = filtrosData.status_ufs ? filtrosData.status_ufs[uf] : [];
                popularSelect(selectStatus, listaStatusUf);
            }
        });

        selectStatus.addEventListener("change", () => {
            const uf = selectUf.value;
            const cidade = selectCidade.value;
            const status = selectStatus.value;

            resetSelect(selectAnoModelo, "Selecione o ano");

            if (!uf || !status) return;

            let listaAnos = [];

            if (cidade) {
                if (filtrosData.anos_cidades && 
                    filtrosData.anos_cidades[uf] && 
                    filtrosData.anos_cidades[uf][cidade]) {
                    listaAnos = filtrosData.anos_cidades[uf][cidade][status] || [];
                }
            } else {
                if (filtrosData.anos_ufs && filtrosData.anos_ufs[uf]) {
                    listaAnos = filtrosData.anos_ufs[uf][status] || [];
                }
            }
            popularSelect(selectAnoModelo, listaAnos);
        });
        const catMatch = encontrarItem(filtrosData.categorias, categoriaParam);

        if (catMatch) {
            selectCategoria.value = catMatch;

            const ufsDaCategoria = filtrosData.ufs ? filtrosData.ufs[catMatch] : [];
            if (ufsDaCategoria && ufsDaCategoria.length > 0) {
                popularSelect(selectUf, ufsDaCategoria);
                    
                const ufMatch = encontrarItem(ufsDaCategoria, ufParam);

                if (ufMatch) {
                    selectUf.value = ufMatch;

                    const cidadesDaUf = filtrosData.cidades ? filtrosData.cidades[ufMatch] : [];
                    popularSelect(selectCidade, cidadesDaUf);

                    let statusParaCarregar = filtrosData.status_ufs ? filtrosData.status_ufs[ufMatch] : [];

                    const cidadeMatch = encontrarItem(cidadesDaUf, cidadeParam);

                    if (cidadeMatch) {
                        selectCidade.value = cidadeMatch;

                        if (filtrosData.status_cidades && filtrosData.status_cidades[ufMatch] && filtrosData.status_cidades[ufMatch][cidadeMatch]) {
                            statusParaCarregar = filtrosData.status_cidades[ufMatch][cidadeMatch];
                        }
                    }

                    popularSelect(selectStatus, statusParaCarregar);

                    const statusMatch = encontrarItem(statusParaCarregar, statusParam);

                    if (statusMatch) {
                        selectStatus.value = statusMatch;

                        let anosParaCarregar = [];

                        if (cidadeMatch) {
                            if (filtrosData.anos_cidades && filtrosData.anos_cidades[ufMatch] && filtrosData.anos_cidades[ufMatch][cidadeMatch]) {
                                anosParaCarregar = filtrosData.anos_cidades[ufMatch][cidadeMatch][statusMatch] || [];
                            }
                        } else {
                            if (filtrosData.anos_ufs && filtrosData.anos_ufs[ufMatch]) {
                                anosParaCarregar = filtrosData.anos_ufs[ufMatch][statusMatch] || [];
                            }
                        }

                        popularSelect(selectAnoModelo, anosParaCarregar);

                        const anoMatch = encontrarItem(anosParaCarregar, anoModeloParam);
                        if (anoMatch) {
                            selectAnoModelo.value = anoMatch;
                        }
                    }
                }
            }
        }

        const inputPriceMin = document.getElementById("price-min");
        const inputPriceMax = document.getElementById("price-max");
            
        if (minValorParam && minValorParam !== "0") {
            inputPriceMin.value = minValorParam;
        }

        if (maxValorParam && maxValorParam !== "0") {
            inputPriceMax.value = maxValorParam;
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

    const btnClear = document.querySelector(".btn-clear");

    if (btnClear) {
        btnClear.addEventListener("click", () => {
            window.location.href = "http://localhost:7777/veiculos";
        });
    }
    
    const btnApply = document.querySelector(".btn-apply");

    btnApply.addEventListener("click", () => {
        const selectCategoria = document.getElementById("filter-categoria");
        const selectUf = document.getElementById("filter-uf");
        const selectCidade = document.getElementById("filter-cidade");
        const selectStatus = document.getElementById("filter-status");
        const selectAnoModelo = document.getElementById("filter-ano-modelo");
        const inputPriceMin = document.getElementById("price-min");
        const inputPriceMax = document.getElementById("price-max");
        const inputBusca = document.getElementById("filter-busca");

        const categoria = encodeURIComponent((selectCategoria.value || "all").toLowerCase());
        const uf = encodeURIComponent((selectUf.value || "all").toLowerCase());
        const cidade = encodeURIComponent((selectCidade.value || "all").toLowerCase());
        const status = encodeURIComponent((selectStatus.value || "all").toLowerCase());
        const anoModelo = encodeURIComponent((selectAnoModelo.value || "0").toLowerCase());
        const precoMin = encodeURIComponent(inputPriceMin.value && Number(inputPriceMin.value) > 0 ? inputPriceMin.value : "0");
        const precoMax = encodeURIComponent(inputPriceMax.value && Number(inputPriceMax.value) > 0 ? inputPriceMax.value : "0");
        let buscaTitulo = inputBusca.value.trim();
        buscaTitulo = encodeURIComponent(buscaTitulo || "all");

        const url = `/veiculos/${categoria}/${uf}/${cidade}/${status}/${precoMin}/${precoMax}/${anoModelo}/${buscaTitulo}/1`;

        window.location.href = url;
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
});