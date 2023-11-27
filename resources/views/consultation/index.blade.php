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

    })
</script>
@endsection