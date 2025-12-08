function saveColvisCheck(table, tableName) {
    // Définir un événement pour sauvegarder les choix des colonnes
    table.on('column-visibility.dt', function (e, settings, column, state) {
        localStorage.setItem('datatable-columns-' + tableName + '-' + column, state);
    });

// Restaurer les choix des colonnes lors du chargement de la page
    table.columns().every(function() {
        var colIndex = this.index();
        var colVisible = localStorage.getItem('datatable-columns-' + tableName + '-' + colIndex);
        if (colVisible !== null) {
            this.visible(colVisible === 'true');
            if (colVisible === 'false') {
                $('th:eq('+colIndex+')').addClass('not-visible');
                table.column(colIndex).visible(false);
                table.columns.adjust().draw(false);
            }
        }
    });
}

