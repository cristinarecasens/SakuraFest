// Selecció d'elements del DOM
const cards = [...document.querySelectorAll('.card')]; 
const filterDia   = document.querySelector('#filterDia');
const filterUbi   = document.querySelector('#filterUbi');
const filterTipus = document.querySelector('#filterTipus');
const filterPreu  = document.querySelector('#filterPreu');
const filterNom   = document.querySelector('#filterNom');

const main = document.querySelector('main');

const ordenarAlf = document.querySelector('#ordreAlf');
const ordenarHora = document.querySelector('#ordreInici');

const botoMostrarFiltres = document.getElementById('boto-mostra-filtres');
const botoMostrarOrdenar = document.getElementById('boto-mostra-ordre');
const divFiltres = document.getElementById('div-filtres');
const divOrdre = document.getElementById('div-ordenar');
const botoEsborrarFiltres = document.getElementById('esborrar-filtres');

// MIRAR SI LA SESSIÓ ESTA OBERT:
localStorage.setItem('logged', '1');        // 1 significa logueado
localStorage.setItem('userName', '<?php echo $nom; ?>');


const estilFiltres = {
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: '20px',
    maxWidth: '900px',
    margin: '0 auto 20px auto',
    flexWrap: 'wrap'
};

// FUNCIÓ PER A APLICAR ELS FILTRES

function aplicarFiltres() {

    // Variables per a guardar els valors seleccionats
    const dia   = filterDia.value;
    const ubi   = filterUbi.value;
    const tipus = filterTipus.value;
    const preu  = filterPreu.value;
    const nom   = filterNom.value.toLowerCase().trim();


    cards.forEach(card => {

        // No hi ha dia seleccionat o el dia coincideix
        const coincideixDia =
            !dia || card.dataset.dia === dia;
            botoEsborrarFiltres.style.display = 'block';

        // No hi ha ubi seleccionada o la ubi coincideix
        const coincideixUbi =
            !ubi || card.dataset.ubi === ubi;
            botoEsborrarFiltres.style.display = 'block';

        // No hi ha tipus seleccionat o el tipus coincideix
        const coincideixTipus =
            !tipus || card.dataset.tipus === tipus;
            botoEsborrarFiltres.style.display = 'block';

        // No hi ha preu seleccionat o el preu coincideix
        const coincideixPreu =
            !preu || card.dataset.preu === preu;
            botoEsborrarFiltres.style.display = 'block';

        // No hi ha text de cerca o el nom conté el text
        const coincideixNom =
            !nom || card.dataset.name.toLowerCase().includes(nom);

        // Només es mostra si es compleixen totes les condicions (son boleans), si algun no es compleix no es mostra
        if (coincideixDia && coincideixUbi && coincideixTipus && coincideixPreu && coincideixNom) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
        actualitzarFiltres();
    });
}

filterDia.addEventListener('change', aplicarFiltres);
filterUbi.addEventListener('change', aplicarFiltres);
filterTipus.addEventListener('change', aplicarFiltres);
filterPreu.addEventListener('change', aplicarFiltres);
filterNom.addEventListener('input', aplicarFiltres);

// Funció per aplicar els filtres

function actualitzarFiltres() {

    // Recorrem tots els selectors
    const selects = [ filterUbi, filterTipus, filterPreu];

    selects.forEach(select => {
        
        for (let i = 0; i < select.options.length; i++) {
            let option = select.options[i];

            // La opció "Tots" sempre visible
            if(option.value === "") {
                option.hidden = false;
                continue;
            }

            // Mirem si alguna card visible té aquest valor
            let existeix = false;
            for(let j = 0; j < cards.length; j++) {
                let card = cards[j];
                // si no està visible i mirem el data amb el nom si és el mateix que la opció select, si coincideix no s'esborra
                if(card.style.display !== "none" && card.dataset[select.id.replace("filter","").toLowerCase()] === option.value) {
                    existeix = true;
                    break;
                }
            }

            // Ocultem o mostrem la opció segons si existeix o no
            option.hidden = !existeix;
        }
    });
}

botoEsborrarFiltres.addEventListener('click', function() {

    // Reiniciar selects
    filterDia.value = "";
    filterUbi.value = "";
    filterTipus.value = "";
    filterPreu.value = "";
    filterNom.value = "";

    // Mostrar todas las cards
    cards.forEach(card => {
        card.style.display = "flex";
    });

    // Ocultar el botón de borrar filtros
    botoEsborrarFiltres.style.display = 'none';

    // Recalcular opciones de los selects
    actualitzarFiltres();
});


botoMostrarFiltres.addEventListener('click', function() {
    // Obtenim l'estil actual
    const actualDisplay = window.getComputedStyle(divFiltres).display;

    if (actualDisplay === 'none') {
       
        // Mostrar
        for (const propietat in estilFiltres) {
            divFiltres.style[propietat] = estilFiltres[propietat];
        }
    } else {
        // Ocultar
        divFiltres.style.display = 'none';
    }
});


botoMostrarOrdenar.addEventListener('click', function() {
    const actualDisplay = window.getComputedStyle(divOrdre).display;

    if (actualDisplay === 'none') {
        // Mostrar
        for (const propietat in estilFiltres) {
            divOrdre.style[propietat] = estilFiltres[propietat];
        }
    } else {
        // Ocultar
        divOrdre.style.display = 'none';
    }
});

// ORDENACIÓ
// Ordenar alfabèticament
ordenarAlf.addEventListener('change', function() {

    ordenarHora.selectedIndex = 0; // per a que el desplegable mostri - 
    var ordre = ordenarAlf.value; // "az" o "za"
    var visibles = [];

    // Agafem les cards que estan visibles
    for (var i = 0; i < cards.length; i++) {
        if (cards[i].style.display !== "none") {
            visibles.push(cards[i]);
        }
    }

    // Ordenem segons el valor del select (az o za)
    visibles.sort(function(a, b) {
        var nomA = a.getAttribute('data-name').toLowerCase();
        var nomB = b.getAttribute('data-name').toLowerCase();

        if (ordre === "az") {
            if (nomA < nomB) return -1;
            if (nomA > nomB) return 1;
            return 0;
        } else {
            if (nomA < nomB) return 1;
            if (nomA > nomB) return -1;
            return 0;
        }
    });

    // Reinsertar les targetes al DOM
    for (var j = 0; j < visibles.length; j++) {
        main.appendChild(visibles[j]);
    }
});


// Ordenar per hora d'inici
ordenarHora.addEventListener('change', function() {

    ordenarAlf.selectedIndex = 0; // per a que el desplegable mostri - 
    var ordre = ordenarHora.value; // "9a21" o "21a9"
    var visibles = [];

    // Agafem les cards que estan visibles
    for (var i = 0; i < cards.length; i++) {
        if (cards[i].style.display !== "none") {
            visibles.push(cards[i]);
        }
    }

    // Ordenem segons el valor del select (az o za)
    visibles.sort(function(a, b) {
        var nomA = a.getAttribute('data-inici').toLowerCase();
        var nomB = b.getAttribute('data-inici').toLowerCase();

        if (ordre === "9a21") {
            if (nomA < nomB) return -1;
            if (nomA > nomB) return 1;
            return 0;
        } else {
            if (nomA < nomB) return 1;
            if (nomA > nomB) return -1;
            return 0;
        }
    });

    // Reinsertar les targetes al DOM
    for (var j = 0; j < visibles.length; j++) {
        main.appendChild(visibles[j]);
    }
});




