@extends('layouts/master')

@section('title', 'Login - alohadoc')

@section('content')
    <div id="landingContainer">
        <h1 id="landingTitle">SELAMAT DATANG DI</h1>
        <div id="landingImageContainer">
            <div>
                <img src="{{ asset('assets/static/LOGO_DARK.png') }}" alt="alohadoc Logo">
            </div>
        </div>
        <div class="container text-center mt-5 px-4">
            <div class="row gx-5">
                <div class="col pointer" id="loginDokter">
                    <div class="card bg-green1" style="width: 18rem;" data-bs-toggle="modal" data-bs-target="#modalLogin">
                        <p class="mt-5">
                            <i class="fa-solid fa-user-doctor" style="color: #ffffff; font-size: 10rem;"></i>
                        </p>
                        <div class="card-body text-white mb-3">
                            <h2 class="fw-medium mb-1">
                                Login Sebagai
                            </h2>
                            <h2>  
                                DOKTER
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col pointer" id="loginPasien">
                    <div class="card bg-orange1" style="width: 18rem;" data-bs-toggle="modal" data-bs-target="#modalLogin">
                        <p class="mt-5">
                            <i class="fa-solid fa-hospital-user" style="color: #ffffff; font-size: 10rem;"></i>
                        </p>
                        <div class="card-body text-white mb-3">
                            <h2 class="fw-medium mb-1">
                                Login Sebagai
                            </h2>
                            <h2>  
                                PASIEN
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5 text-white">
                <h3 class="fw-medium">
                    Belum punya akun?
                    <span id="register" data-bs-toggle="modal" data-bs-target="#modalRegiter">
                        DAFTAR DI SINI!
                    </span>
                </h3>
            </div>
        </div>
    </div>

    {{-- Modal Register --}}
    @include('modals/register')

    {{-- Modal Login --}}
    @include('modals/login')
@endsection

@section('extra-css')
<style>
    body{
        background-color: #121528;
    }

    #landingContainer{
        position: relative;
        padding: 5rem 25%;
    }

    #landingTitle{
        text-align: center;
        font-weight: 500;
        color: #FFFFFF;
    }

    #landingImageContainer{
        width: 100%
    }

    #landingImageContainer div{
        width: 40%;
        margin: 0 auto;
    }

    #landingImageContainer div img{
        width: 100%;
    }

    #loginDokter .card, #loginPasien .card{
        transition: all 0.25s ease-in;
    }

    #loginDokter .card:hover, #loginPasien .card:hover{
        opacity: 0.7;
    }

    #register{
        font-size: 22px;
        font-weight: 700;

        text-decoration: underline;
        cursor: pointer;
    }
</style>
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        $('#loginDokter').on('click', function(){
            $('#modalLoginLabel').text('Login Sebagai Dokter')
            $('#loginRole').val('doctor')
        })
        
        $('#loginPasien').on('click', function(){
            $('#modalLoginLabel').text('Login Sebagai Pasien')
            $('#loginRole').val('patient')
        })
    })
</script>
@endsection