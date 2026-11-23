document.addEventListener('submit', function (event) {
    const message = event.target.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});

const search = document.querySelector('#reservation-search');
if (search) {
    const rows = document.querySelector('#reservations-table tbody');
    const searchError = document.querySelector('#search-error');
    let requestNumber = 0;
    let timeout;

    search.addEventListener('input', function () {
        clearTimeout(timeout);
        requestNumber++;
        const currentRequest = requestNumber;
        const url = search.getAttribute('data-url') + '?q=' + encodeURIComponent(search.value);

        timeout = setTimeout(function () {
            fetch(url)
                .then(function (response) {
                    if (!response.ok || response.redirected) {
                        throw new Error('Recherche indisponible');
                    }
                    return response.text();
                })
                .then(function (html) {
                    if (currentRequest === requestNumber) {
                        rows.innerHTML = html;
                        searchError.hidden = true;
                    }
                })
                .catch(function () {
                    if (currentRequest === requestNumber) {
                        searchError.hidden = false;
                    }
                });
        }, 180);
    });
}
