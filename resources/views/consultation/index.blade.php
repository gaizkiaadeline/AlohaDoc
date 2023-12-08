@extends('layouts/master')

@section('title', 'Konsultasi - alohadoc')

@section('content')
    <div class="content_header">
        <h2>List Konsultasi</h2>
        
        <div>
            @if(auth()->user()->role == 'patient')
            <a href="#" class="btn btn-primary mb-3">
                <span class="fas fa-plus"></span>&nbsp;&nbsp;&nbsp;Request Konsultasi
            </a>
            @endif
        </div>
    </div>
    <div>
        @include('consultation.__table')
    </div>
@endsection

@section('extra-css')
<style>
    .content_header{
        padding: 3rem 5%;
        display: flex;
        justify-content: space-between;
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
                    "data": "created_at",
                    "name": "created_at.timestamp",
                    "render": function(data, type, row, meta) {
                        return row.created_at.display
                    },
                    "targets": 1
                },
                {
                    "data": "name",
                    "name": "name",
                    "targets": 2
                },
                {
                    "data": "specialist_id",
                    "name": "specialist_id",
                    "targets": 3
                },
                {
                    "data": "status",
                    "name": "status",
                    "targets": 4
                },
                {
                    "data": "recipe_avail",
                    "name": "recipe_avail",
                    "targets": 5
                },
                {
                    "data": 'actions',
                    "targets": 6,
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
                    "targets": [7]
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
                        columnArr = ['specialist_id', 'status']

                        var columns = table.settings().init().columnDefs;
                        var _changeInterval = null;
                        let name = columns[i].name

                        if (columnArr.includes(name)) {
                            var column = this;

                            
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