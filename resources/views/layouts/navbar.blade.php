<nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
        <img src="{{ asset('assets/static/LOGO_LIGHT.png') }}" alt="Logo" width="200" class="d-inline-block align-text-top mr-3" style="margin-right: 1rem;">
        </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 pt-2">
          <li class="nav-item">
            <a class="nav-link active" href="/consultation">Konsultasi</a>
          </li>
          @if(auth()->user()->role == 'admin')
          <li class="nav-item" style="margin-left: 1rem;">
            <a class="nav-link active" href="/user">User Management</a>
          </li>
          @elseif(auth()->user()->role == 'doctor')
          <li class="nav-item" style="margin-left: 1rem;">
            <a class="nav-link active" href="/schedule">Jadwal</a>
          </li>
          @endif
        </ul>
        <form action="{{ route('logout') }}" method="POST">
        @csrf
            <button class="btn bg-orange1 text-white fw-medium" type="submit">LOGOUT</button>
        </form>
      </div>
    </div>
  </nav>