@extends('layouts/master')

@section('title', 'Resep - alohadoc')

@section('content')
    <div class="content_header">
        <h2>Tambah/Ubah Resep</h2>
    </div>
    <div style="margin: 0 5%;">
        <form action="{{ route('consultation.store-recipe', ['consultation' => $consultation->id]) }}" method="post">
        @csrf
            <div class="form-group mb-3">
                <label for="recipe">Masukkan Resep Obat dan Notes:</label>
                <textarea class="form-control" name="recipe" id="recipe" required rows="10">{{ $consultation->recipe }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
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