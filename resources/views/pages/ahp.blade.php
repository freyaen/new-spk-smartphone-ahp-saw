@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h6 class="card-title">Analytical Hierarchy Process (AHP)</h6>

        <form id="comparisonForm" action="{{ route('ahp.store') }}" method="POST">
            @csrf

            <div class="alert alert-info mb-4">
                <p class="mb-0">
                    <strong>Skala Saaty (1-9) untuk Perbandingan Kriteria:</strong>
                </p>
                <ul>
                    <li>1: Sama penting</li>
                    <li>2: Mendekati Sedikit lebih penting</li>
                    <li>3: Sedikit lebih penting</li>
                    <li>4: Mendekati Lebih penting</li>
                    <li>5: Lebih penting</li>
                    <li>6: Mendekati Sangat lebih penting</li>
                    <li>7: Sangat lebih penting</li>
                    <li>8: Mendekati Mutlak lebih penting</li>
                    <li>9: Mutlak lebih penting</li>
                </ul>
            </div>

            <section class="mt-5">
                <h5 class="section-title">
                    Matriks Perbandingan Berpasangan (Input Nilai)
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kriteria</th>
                                @foreach ($criterias as $c)
                                <th>{{ $c->code }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($criterias as $i => $a)
                            <tr>
                                <th class="bg-light">{{ $a->code }}</th>
                                @foreach ($criterias as $j => $b)
                                    @php
                                        $isDiagonal = ($i == $j);
                                        // Cari nilai yang ada di database untuk pasangan ini (A:B)
                                        $existing = $pairs->where('criteria_a_uuid', $a->uuid)
                                                          ->where('criteria_b_uuid', $b->uuid)
                                                          ->first();
                                    @endphp
                                    
                                    @if ($isDiagonal)
                                        {{-- Sel Diagonal: Selalu 1, tidak bisa diubah --}}
                                        <td class="bg-primary text-white font-weight-bold">1</td>
                                        <input type="hidden" name="criteria_a[]" value="{{ $a->uuid }}">
                                        <input type="hidden" name="criteria_b[]" value="{{ $b->uuid }}">
                                        <input type="hidden" name="value[]" value="1">
                                    @else
                                        {{-- Sel Non-Diagonal: Bisa diisi --}}
                                        <td>
                                            <input type="hidden" name="criteria_a[]" value="{{ $a->uuid }}">
                                            <input type="hidden" name="criteria_b[]" value="{{ $b->uuid }}">
                                            <input type="number" 
                                                   name="value[]" 
                                                   class="form-control text-center comparison-input" 
                                                   step="0.000001" {{-- Menggunakan step 0.01 agar bisa input resiprokal seperti 0.11 atau 1/9 --}}
                                                   min="0.000001" 
                                                   max="9" 
                                                   required
                                                   value="{{ $existing ? $existing->value : '' }}">
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary">
                        Hitung AHP & Uji Konsistensi
                    </button>
                </div>
            </section>
        </form>

        <hr class="mt-5 mb-5">

        @if (empty($result))
            <div class="alert alert-warning text-center">
                Silakan isi dan kirimkan Matriks Perbandingan di atas untuk melihat hasil perhitungan AHP.
            </div>
        @else
        <section class="mt-5">
            <h5 class="section-title">
                Matriks Normalisasi & Bobot Prioritas
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kriteria</th>
                            @foreach ($criterias as $c)
                            <th>{{ $c->code }}</th>
                            @endforeach
                            <th>Jumlah Baris</th>
                            <th>Bobot Prioritas (W)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['normalized'] as $i => $row)
                        @php $rowSum = array_sum($row); @endphp
                        <tr>
                            <th class="bg-light">{{ $criterias[$i]->code }}</th>
                            @foreach ($row as $val)
                            <td>{{ number_format($val, 4) }}</td>
                            @endforeach
                            <td class="bg-light">{{ number_format($rowSum, 4) }}</td>
                            <td class="font-weight-bold">{{ number_format($result['weights'][$i], 4) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <hr>

        <section class="mt-5">
            <h5 class="section-title">
                Matriks Penjumlahan Tiap Baris (Vektor Jumlah)
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kriteria</th>
                            @foreach ($criterias as $c)
                            <th>{{ $c->code }}</th>
                            @endforeach
                            <th>Total Baris ($\lambda$ Hitung)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['sumMatrix'] as $i => $row)
                        <tr>
                            <th class="bg-light">{{ $criterias[$i]->code }}</th>
                            @foreach ($criterias as $j => $c)
                            <td>{{ number_format($row[$j], 4) }}</td>
                            @endforeach
                            <td class="bg-light font-weight-bold">{{ number_format($row['sum'], 4) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <hr>

        <section class="mt-5">
            <h5 class="section-title">
                Uji Konsistensi Rasio (Consistency Ratio)
            </h5>
            
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kriteria</th>
                            <th>Total Baris ($\lambda$ Hitung)</th>
                            <th>Bobot Prioritas (W)</th>
                            <th>Rasio ($\lambda$ Hitung / W)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['sumMatrix'] as $i => $row)
                        @php $ratio = $row['sum'] / $result['weights'][$i]; @endphp
                        <tr>
                            <th class="bg-light">{{ $criterias[$i]->code }}</th>
                            <td>{{ number_format($row['sum'], 4) }}</td>
                            <td>{{ number_format($result['weights'][$i], 4) }}</td>
                            <td>{{ number_format($ratio, 4) }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-secondary">
                            <th colspan="3" class="text-right">Rata-rata ($\lambda$ Max)</th>
                            <th class="font-weight-bold">{{ number_format($result['lambdaMax'], 4) }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Hasil Perhitungan</h6>
                            <div class="mb-3">
                                <strong>$\lambda$ Max (Lambda Maksimum):</strong>
                                <span class="float-right">{{ number_format($result['lambdaMax'], 4) }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>CI (Consistency Index):</strong>
                                <span class="float-right">{{ number_format($result['CI'], 4) }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>CR (Consistency Ratio):</strong>
                                <span class="float-right font-weight-bold {{ $result['CR'] > 0.1 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($result['CR'], 4) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Status Konsistensi</h6>
                            @if ($result['CR'] > 0.1)
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="alert-heading mb-1">❌ Tidak Konsisten!</h6>
                                        <p class="mb-0">CR = {{ number_format($result['CR'], 4) }} > 0.10</p>
                                        <small>Silakan ulangi input perbandingan untuk mencapai CR $\le 0.10$.</small>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="alert-heading mb-1">✅ Konsisten!</h6>
                                        <p class="mb-0">CR = {{ number_format($result['CR'], 4) }} $\le$ 0.10</p>
                                        <small>Perbandingan konsisten dan Bobot Prioritas dapat digunakan.</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mt-3">
                                <strong>Interpretasi:</strong>
                                <ul class="mb-0">
                                    <li>CR $\le$ 0.10 → Konsisten (Hasil valid)</li>
                                    <li>CR > 0.10 → Tidak Konsisten (Perlu perbaikan input)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif {{-- End check $result --}}
    </div>
</div>

<style>
    .section-title {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 20px;
        color: #495057;
        font-weight: 600;
    }
    
    .comparison-input {
        max-width: 80px; /* Ukuran yang disesuaikan untuk input */
        display: inline-block;
    }
    
    .table th {
        vertical-align: middle !important;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Validasi input perbandingan
        document.querySelectorAll(".comparison-input").forEach(input => {
            input.addEventListener("change", function() {
                let value = parseFloat(this.value);
                
                // Pastikan nilai tidak nol
                if (value === 0) {
                    this.value = 1;
                    alert("Nilai tidak boleh nol. Nilai minimal adalah 0.01 (untuk 1/9) atau 1.");
                    return;
                }
                
                // Jika input positif
                if (value > 0) {
                    // Batas atas (9)
                    if (value > 9) {
                        this.value = 9;
                        alert("Nilai maksimal adalah 9.");
                    }
                    // Batas bawah (1/9)
                    const reciprocalLimit = 1/9; // Sekitar 0.111
                    if (value < reciprocalLimit && value >= 0.01) {
                         // Biarkan pengguna memasukkan nilai resiprokal yang valid (e.g., 0.125 atau 0.111)
                    } else if (value < 1 && value < reciprocalLimit) {
                         this.value = reciprocalLimit.toFixed(2); // Dibatasi di 1/9
                         alert("Nilai minimal (untuk kebalikan) adalah 1/9, sekitar 0.11.");
                    }
                } else {
                    // Mencegah input negatif
                    this.value = 1;
                    alert("Input harus berupa nilai positif.");
                }
            });
        });
    });
</script>
@endsection