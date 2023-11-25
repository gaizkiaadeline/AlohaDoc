@extends('layouts/master')

@section('title', 'User Management - alohadoc')

@section('content')
    <div class="content_header">
        <h2>List User</h2>
    </div>
    <div>
        @include('admin.user.__table')
    </div>
@endsection

@section('extra-css')
<style>
    .content_header{
        padding: 3rem 5% 1rem 5%;
        display: flex;
        justify-content: space-between;
    }

    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
        border: 1px solid #ced4da !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding: .375rem .75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #555 transparent transparent transparent;
        border-style: solid;
        border-width: 5px 4px 0 4px;
        height: 0;
        left: 50%;
        margin-left: -4px;
        margin-top: -2px;
        position: absolute;
        top: 50%;
        width: 0;
    }
</style>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        $(document).on('click', '.Status', function(){
            swal.fire({
                title: 'Apakah anda yakin?',
                text: "Status keaktifan user ini akan diganti!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ganti Status',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    let routeUrl = $(this).attr("href")
                    window.location.href = routeUrl
                }
            });
        })

        //Inisialisasi Datatable
        initUserDatatable();
    })

    function initUserDatatable(){
        let table = $('#user-list').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route($route) }}",
            "columnDefs": [
                {
                    "data": null,
                    "targets": 0
                },
                {
                    "data": "name",
                    "name": "name",
                    "targets": 1
                },
                {
                    "data": "email",
                    "name": "email",
                    "targets": 2
                },
                {
                    "data": "jenis_kelamin",
                    "name": "jenis_kelamin",
                    "targets": 3
                },
                {
                    "data": "role",
                    "name": "role",
                    "targets": 4
                },
                {
                    "data": "telephone",
                    "name": "telephone",
                    "targets": 5
                },
                {
                    "data": "is_active",
                    "name": "is_active",
                    "targets": 6
                },
                {
                    "data": "created_at",
                    "name": "created_at.timestamp",
                    "render": function(data, type, row, meta) {
                        return row.created_at.display
                    },
                    "targets": 7
                },
                {
                    "data": 'actions',
                    "targets": 8,
                    "render": function(data, type, row, meta) {
                        if (data !== '') {
                            let actionContent = `<div style='display: flex; gap:0.5em;'>`;

                            data.map((button, idx) => {
                                actionContent += 
                                `<button href="${button.route}" buttonId="${button.attr_id}" class="btn btn-${button.btnStyle} btn-sm ${button.btnClass}">
                                        <div style="display:flex; align-items:center;">
                                            <span class="${button.icon}"></span>
                                            <span style="margin-left: 0.25em; text-wrap: nowrap;">${button.label}</span>
                                        </div>
                                </button>`;
                            })

                            actionContent += `</div>`

                            return actionContent;
                        } else {
                            return '';
                        }
                    }
                },
                {
                    "searchable": false,
                    "orderable": false,
                    "targets": [8]
                }
            ],
            search: {
                smart: false,
                "caseInsensitive": false,
                return: true
            },
            "createdRow": function(row, data, dataIndex) {
                $(row).find('td').first().text((table.page() * table.page.len()) + dataIndex + 1);
            },
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function(i) {
                        columnArr = ['jenis_kelamin', 'role', 'is_active']

                        var columns = table.settings().init().columnDefs;
                        var _changeInterval = null;
                        let name = columns[i].name

                        if (columnArr.includes(name)) {
                            var column = this;

                            if (name == 'jenis_kelamin') {
                                let jenisKelaminList = {
                                    'Pria': 'Pria',
                                    'Wanita': 'Wanita'
                                }

                                let div = $(`<div id="wrap-jenis_kelamin" class="form-group"></div>`).appendTo($(column.footer()).empty())

                                var select = $(`<select id="select_jenis_kelamin" class="form-control"><option value="" selected>Pilih Jenis Kelamin</option></select>`)
                                    .appendTo($('#wrap-jenis_kelamin'))
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val, false, false).draw();
                                    });


                                for (var key in jenisKelaminList) {
                                    $('#select_jenis_kelamin').append(`<option value="${key}">${jenisKelaminList[key]}</option>`);
                                }

                                div.append(select)
                                $("#select_jenis_kelamin").select2({theme: "bootstrap"})
                            }
                            else if(name == 'role'){
                                let jenisKelaminList = {
                                    'Dokter': 'doctor',
                                    'Pasien': 'patient',
                                    'Admin': 'admin'
                                }

                                let div = $(`<div id="wrap-role" class="form-group"></div>`).appendTo($(column.footer()).empty())

                                var select = $(`<select id="select_role" class="form-control"><option value="" selected>Pilih Role</option></select>`)
                                    .appendTo($('#wrap-role'))
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val, false, false).draw();
                                    });


                                for (var key in jenisKelaminList) {
                                    $('#select_role').append(`<option value="${key}">${jenisKelaminList[key]}</option>`);
                                }

                                div.append(select)
                                $("#select_role").select2({theme: "bootstrap"})
                            }
                            else if(name == 'is_active'){
                                let jenisKelaminList = {
                                    'Active': 'Active',
                                    'Not Active': 'Not Active',
                                }

                                let div = $(`<div id="wrap-active" class="form-group"></div>`).appendTo($(column.footer()).empty())

                                var select = $(`<select id="select_active" class="form-control"><option value="" selected>Pilih Status</option></select>`)
                                    .appendTo($('#wrap-active'))
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val, false, false).draw();
                                    });


                                for (var key in jenisKelaminList) {
                                    $('#select_active').append(`<option value="${key}">${jenisKelaminList[key]}</option>`);
                                }

                                div.append(select)
                                $("#select_active").select2({theme: "bootstrap"})
                            }
                        }
                    });
            },
        })

        // Add an event listener to the search input field
        $('#user-list_filter input').off().on('keyup', function (e) {
            // Check if the user pressed Enter (key code 13)
            if (e.keyCode === 13) {
                // Perform the search
                table.search(this.value).draw();
            }
        });
    }
</script>
@endsection