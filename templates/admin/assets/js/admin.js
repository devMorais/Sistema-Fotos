
$(document).ready(function () {
    $('#tabelaProdutos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        ajax: 'http://localhost/blog/admin/produtos/datatable',
        processing: true,
        serverSide: true
    })

});

$(document).ready(function () {
    $('#tabelaCategorias').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    })

});

$(document).ready(function () {
    $('#tabelaUsuarios').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    })

});

$(document).ready(function () {
    $('#summernote').summernote();
});


