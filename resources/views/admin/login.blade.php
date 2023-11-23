@extends('layouts/master')

@section('title', 'Login - alohadoc')

@section('content')
    <div class="container position-relative">
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h1 class="modal-title fs-5">Login Sebagai Admin</h1>
                    </div>
                    <form action="{{ route('login') }}" method="post">
                    @csrf
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                            <input type="hidden" name="role" id="loginRole" value="admin">
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection