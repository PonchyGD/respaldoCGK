document.addEventListener('DOMContentLoaded', () => {
    obtenerPlatillos();
});

function obtenerPlatillos() {
    fetch('get.php')
        .then(response => response.json())
        .then(platillos => mostrarPlatillos(platillos))
        .catch(error => console.error('Error al obtener los platillos:', error));
}

function mostrarPlatillos(platillos) {
    const contenedorPlatillos = document.getElementById('contenedorPlatillos');

    platillos.forEach(platillo => {
        const platilloElement = document.createElement('div');
        platilloElement.classList.add('platillo');

        platilloElement.innerHTML = `
            <img src="${platillo.ImagenPath}" alt="${platillo.NombreMenu}">
            <h3>${platillo.NombreMenu}</h3>
            <p>${platillo.Descripcion}</p>
            <p>Precio: $${platillo.Precio.toFixed(2)}</p>
        `;

        contenedorPlatillos.appendChild(platilloElement);
    });
}
