@extends('layouts/master')

@section('title', 'Konsultasi - alohadoc')

@section('content')
    <div class="content_header">
        <h2>List Konsultasi</h2>
        
        <div>
            @if(auth()->user()->role == 'patient')
            <a href="#" class="btn btn-primary mb-3" id="addConsultation" data-bs-toggle="modal" data-bs-target="#modalAddConsultation">
                <span class="fas fa-plus"></span>&nbsp;&nbsp;&nbsp;Request Konsultasi
            </a>
            @endif
        </div>
    </div>
    <div>
        @include('consultation.__table')
    </div>
    @include('consultation.modals.add')
@endsection

@section('extra-css')
<style>
    .content_header{
        padding: 3rem 5%;
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

    #specialistIdContainer .select2-container{
        width: 100% !important;
    }
    #sessionIdContainer .select2-container{
        width: 100% !important;
    }

</style>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        $(document).on('click', '.Cancel', function(){
            swal.fire({
                title: 'Apakah anda yakin?',
                text: "Request konsultasi akan dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Cancel Request',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    let routeUrl = $(this).attr("href")
                    window.location.href = routeUrl
                }
            });
        })

        $(document).on('click', '.Activate', function(){
            swal.fire({
                title: 'Apakah anda yakin?',
                text: "Request konsultasi akan dilanjutkan sesuai status yang dipilih!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ganti Status Request',
                reverseButtons: true
                }).then((result) => {
                if (result.isConfirmed) {
                    let routeUrl = $(this).attr("href")
                    window.location.href = routeUrl
                }
            });
        })

        $(document).on('click', '.Consultation', function(){
            let routeUrl = $(this).attr("href")
            window.location.href = routeUrl
        })

        $(document).on('click', '.Recipe', function(){
            let routeUrl = $(this).attr("href")
            window.location.href = routeUrl
        })

        //Inisialisasi Datatable
        initConsultationDatatable();

        $('#specialist').select2({
            theme: "bootstrap",
            dropdownParent: $('#modalAddConsultation'),
            minimumResultsForSearch: 1
        })

        $('#session').select2({
            theme: "bootstrap",
            dropdownParent: $('#modalAddConsultation'),
            ajax: {
                url: "{{ route('consultation.get-session') }}",
                // minimumResultsForSearch: -1,
                data: function (params) {
                    return {
                        search: params.term,
                        specialist: $('#specialist').val(),
                        date : $('#date').val()
                    }
                },
                processResults: function (data) {
                    if(data.length == 0){
                        $('#warningDate').text('Tidak ada sesi yang tersedia untuk spesialis dan tanggal yang Anda pilih!')
                    }
                    else{
                        $('#warningDate').text('')
                    }
                    
                    return data
                }
            },
        });

        $('#sessionIdForm').css('display', 'none')

        $('#specialist').on('change', function(){
            if($(this).val() == '-'){
                $('#sessionIdForm').css('display', 'none')
            }
            else{
                if($('#date').val()){
                    $('#sessionIdForm').css('display', 'block')
                }
            }
        })

        $('#date').on('change', function(){
            if(!$(this).val()){
                $('#sessionIdForm').css('display', 'none')
            }
            else{
                if($('#specialist').val() != '-'){
                    $('#sessionIdForm').css('display', 'block')
                }

                $('#session').val(null).trigger('change');
            }
        })

        $('#date').on('input', function () {
            let selectedDate = $(this).val();
            if (selectedDate < new Date().toISOString().split('T')[0]) {
                $(this).val(new Date().toISOString().split('T')[0])
                $('#warningDate').text('Tanggal hanya bisa diisi dimulai dari hari ini!')
            }
            else{
                $('#warningDate').text('');
            }
        });
    })

    function initConsultationDatatable(){
        let table = $('#consultation-list').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route($route) }}",
            "columnDefs": [
                {
                    "data": null,
                    "targets": 0
                },
                {
                    "data": "consultation_date",
                    "name": "consultation_date.timestamp",
                    "render": function(data, type, row, meta) {
                        return row.consultation_date.display
                    },
                    "targets": 1
                },
                {
                    "data": "session",
                    "name": "session",
                    "targets": 2
                },
                {
                    "data": "name",
                    "name": "name",
                    "targets": 3
                },
                {
                    "data": "specialist_id",
                    "name": "specialist_id",
                    "targets": 4
                },
                {
                    "data": "status",
                    "name": "status",
                    "targets": 5
                },
                {
                    "data": "recipe_avail",
                    "name": "recipe_avail",
                    "targets": 6
                },
                {
                    "data": 'actions',
                    "targets": 7,
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
                    "targets": [6]
                }
            ],
            order: [[1, 'desc']],
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
                        columnArr = ['status']

                        var columns = table.settings().init().columnDefs;
                        var _changeInterval = null;
                        let name = columns[i].name

                        if (columnArr.includes(name)) {
                            var column = this;

                            if (name == 'status') {
                                let statusList = {
                                    'Proses Request' : 'Proses Request',
                                    'Booked' : 'Booked',
                                    'Request Dibatalkan' : 'Request Dibatalkan',
                                    'Request Ditolak' : 'Request Ditolak',
                                    'Konsultasi Aktif' : 'Konsultasi Aktif',
                                    'Menunggu Resep' : 'Menunggu Resep',
                                    'Konsultasi Selesai' : 'Konsultasi Selesai'
                                }

                                let div = $(`<div id="wrap-status" class="form-group"></div>`).appendTo($(column.footer()).empty())

                                var select = $(`<select id="select_status" class="form-control"><option value="" selected>Pilih Status</option></select>`)
                                    .appendTo($('#wrap-status'))
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val, false, false).draw();
                                    });


                                for (var key in statusList) {
                                    $('#select_status').append(`<option value="${key}">${statusList[key]}</option>`);
                                }

                                div.append(select)
                                $("#select_status").select2({theme: "bootstrap"})
                            }
                        }
                    });
            }
        })

        // Add an event listener to the search input field
        $('#consultation-list_filter input').off().on('keyup', function (e) {
            // Check if the user pressed Enter (key code 13)
            if (e.keyCode === 13) {
                // Perform the search
                table.search(this.value).draw();
            }
        });
    }
</script>
@endsection