@extends('layouts.app')

@section('title', 'Rekomendasi Menu AI')
@section('page-title', 'Rekomendasi Menu AI')
@section('breadcrumb', 'Rekomendasi Menu AI')

@push('styles')
<style>

.form-control{
    width:100%;
    padding:10px;
    border-radius:10px;
    border:1px solid #dbeafe;
    background:#f8fbff;
}

.btn-main{
    background:#2563eb;
    color:white;
    border:none;
    border-radius:10px;
    padding:10px 18px;
    cursor:pointer;
}

.btn-main:hover{
    opacity:.9;
}

.error-text{
    color:red;
    margin-top:5px;
    display:block;
}

.ai-result{
    white-space: pre-line;
    line-height:1.7;
}

</style>
@endpush

@section('content')

<div class="main">
    <div class="dashboard-container">
        <div class="content">

            <!-- FORM -->
            <div class="card filter-card">

                <form action="{{ route('ai.generate') }}" method="POST">

                    @csrf

                    <div class="filter-grid">

                        <div class="form-group">

                            <label>Bahan yang tersedia</label>

                            <input
                                type="text"
                                name="bahan"
                                class="form-control"
                                placeholder="Contoh: ayam, wortel, kol"
                                value="{{ old('bahan') }}"
                                required
                            >

                            @error('bahan')
                                <small class="error-text">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>

                        <div
                            class="form-group"
                            style="display:flex;align-items:end;"
                        >

                            <button
                                type="submit"
                                class="btn-main"
                            >
                                Cari dengan AI
                            </button>

                        </div>

                    </div>

                </form>

            </div>

            {{-- Error AI --}}
            @error('ai')
                <div class="card info-card">
                    <div class="error-text">
                        {{ $message }}
                    </div>
                </div>
            @enderror

            {{-- Hasil --}}
            @if(session('hasil'))

                <div class="card info-card">

                    <div class="card-title">
                        Hasil AI
                    </div>

                    <div class="ai-result">

                        {{ session('hasil') }}

                    </div>

                </div>

            @endif

        </div>
    </div>
</div>

@endsection