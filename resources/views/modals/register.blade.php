<div class="modal hide fade" id="modalRegister">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalRegisterLabel">Register</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            <form action="{{ route('register') }}" method="post">
            @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name">Nama Lengkap:</label>
                        <input type="text" class="form-control" name="name" id="name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group mb-3">
                        <label for="jenis_kelamin">Jenis Kelamin:</label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                            <option value="Pria" selected>Pria</option>
                            <option value="Wanita">Wanita</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="role">Daftar Sebagai:</label>
                        <select class="form-control" name="role" id="role">
                            <option value="patient" selected>Pasien</option>
                            <option value="doctor">Dokter</option>
                        </select>
                    </div>
                    <div class="form-group mb-3" id="specialistIdForm">
                        <label for="specialist">Spesialis:</label>
                        <div id="specialistIdContainer">
                            <select class="form-control" name="specialist" id="specialist">
                                <option value="-" selected>- Pilih Spesialis Dokter -</option>
                                @foreach($specialists as $specialist)
                                    <option value='{{ $specialist->id }}'>{{ $specialist->specialist }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="telephone">Nomor Telepon:</label>
                        <input type="text" class="form-control" name="telephone" id="telephone">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_confirmation">Konfirmasi Password:</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>