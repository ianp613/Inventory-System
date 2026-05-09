if(document.getElementById("terminals")){
    const add_terminal                         = new bootstrap.Modal(document.getElementById('add_terminal'),unclose);
    add_terminal.show()
    let terminalTable = new DataTable('#tb_terminals',{
        rowCallback: function(row) {
            $(row).addClass("trow");
        },
        scrollX: false,
        columnDefs: [
            {
                target: 0,
                visible: false,
                searchable: false
            },
            {
                className: 'dt-left',
                targets: '_all'
            }
        ],
        autoWidth: false,
        language: {
           sLengthMenu: "Show _MENU_entries",
           search: "<button id=\"add_router_btn\" data-bs-toggle=\"modal\" data-bs-target=\"#add_terminal\" class=\"btn btn-sm btn-dark me-3\"><span class=\"fa fa-plus\"></span> Add Router</button> Search: "
        }
    });
}