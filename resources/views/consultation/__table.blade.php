<div style="overflow-x: auto; margin: 0 5%;">
    <table class="table table-bordered table-hover" id="consultation-list">
        <thead style="white-space:nowrap">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Sesi</th>
                @if(in_array(auth()->user()->role, ['patient', 'admin']))
                <th>Nama Dokter</th>
                @elseif(auth()->user()->role == 'doctor')
                <th>Nama Pasien</th>
                @endif
                <th>Spesialis Dokter</th>
                <th>Status</th>
                <th>Resep Tersedia</th>
                <th width="80">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot style="display: table-header-group !important;">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>