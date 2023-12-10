<div class="modal hide fade" tabindex="-1" id="modalAddConsultation">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalAddConsultation">Request Konsultasi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            <form action="{{ route('consultation.store') }}" method="post">
            @csrf
                <div class="modal-body">
                    <div class="form-group mb-3" id="specialistIdForm">
                        <label for="specialist">Pilih Spesialis Dokter:</label>
                        <div id="specialistIdContainer">
                            <select class="form-control" name="specialist" id="specialist" required>
                                <option value="-" selected>- Pilih Spesialis Dokter -</option>
                                @foreach($specialists as $specialist)
                                    <option value='{{ $specialist->id }}'>{{ $specialist->specialist }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="date">Pilih Tanggal:</label>
                        <input type="date" class="form-control" name="date" id="date" required>
                        <small class="text-danger" id="warningDate"></small>
                    </div>
                    <div class="form-group mb-3" id="sessionIdForm">
                        <label for="session">Pilih Sesi Dokter:</label>
                        <div id="sessionIdContainer">
                            <select class="form-control" name="session" id="session" required>
                                <option value="-" selected>- Pilih Sesi -</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Request</button>
                </div>
            </form>
        </div>
    </div>
</div>