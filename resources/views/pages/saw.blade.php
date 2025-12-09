@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <h6 class="card-title">SAW - Simple Additive Weighting</h6>

        <!-- Section 1: Matriks Keputusan -->
        <section class="mt-4">
            <h5 class="section-title">
                Matriks Keputusan
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="10%">Kode</th>
                            <th width="20%">Nama Smartphone</th>
                            @foreach($criterias as $c)
                            <th class="text-center">
                                {{ $c->code }}<br><small>{{ $c->type == 'cost' ? 'Cost' : 'Benefit' }}</small></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alternatives as $alt)
                        <tr>
                            <td class="font-weight-bold">{{ $alt['code'] }}</td>
                            <td>{{ $alt['name'] }}</td>
                            @foreach($criterias as $index => $c)
                            <td class="text-center">{{ number_format($alt['criterias'][$index]['value'], 2) ?? 0 }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="2" class="text-right">Tipe Kriteria:</th>
                            @foreach($criterias as $c)
                            <th class="text-center {{ $c->type == 'cost' ? 'text-danger' : 'text-success' }}">
                                {{ $c->type == 'cost' ? 'Min' : 'Max' }}
                            </th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- Section 2: Normalisasi Matriks -->
        <section class="mt-5">
            <h5 class="section-title">
                Matriks Normalisasi
            </h5>
            <div class="alert alert-info">
                <strong>Rumus Normalisasi:</strong><br>
                Benefit: r<sub>ij</sub> = x<sub>ij</sub> / max(x<sub>ij</sub>)<br>
                Cost: r<sub>ij</sub> = min(x<sub>ij</sub>) / x<sub>ij</sub>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="10%">Kode</th>
                            <th width="20%">Nama Smartphone</th>
                            @foreach($criterias as $c)
                            <th class="text-center">
                                {{ $c->code }}<br><small>{{ $c->type == 'cost' ? 'Min/Value' : 'Value/Max' }}</small>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($normalized as $alt)
                        <tr>
                            <td class="font-weight-bold">{{ $alt['code'] }}</td>
                            <td>{{ $alt['name'] }}</td>
                            @foreach($criterias as $c)
                            <td class="text-center">{{ number_format($alt['values'][$c->code] ?? 0, 4) }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="2" class="text-right">Bobot AHP:</th>
                            @foreach($criterias as $c)
                            <th class="text-center font-weight-bold">
                                {{ number_format($c->weight, 4) }}
                            </th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <!-- Section 3: Perhitungan Weighted Sum -->
        <section class="mt-5">
            <h5 class="section-title">
                Perhitungan Weighted Sum
            </h5>
            <div class="alert alert-info">
                <strong>Rumus:</strong> V<sub>i</sub> = Σ (w<sub>j</sub> × r<sub>ij</sub>)
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="10%">Kode</th>
                            <th width="20%">Nama Smartphone</th>
                            @foreach($criterias as $c)
                            <th class="text-center">
                                {{ $c->code }}<br><small>w={{ number_format($c->weight, 3) }}</small></th>
                            @endforeach
                            <th class="bg-primary text-white">Total Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weightedSum as $alt)
                        <tr>
                            <td class="font-weight-bold">{{ $alt['code'] }}</td>
                            <td>{{ $alt['name'] }}</td>
                            @foreach($criterias as $c)
                            <td class="text-center">{{ number_format($alt['weighted'][$c->code] ?? 0, 4) }}</td>
                            @endforeach
                            <td class="text-center font-weight-bold bg-light">
                                {{ number_format($alt['total'] ?? 0, 4) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section 4: Ranking Akhir -->
        <section class="mt-5">
            <h5 class="section-title">
                Ranking Akhir Smartphone
            </h5>

            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="card-title">Rekomendasi Terbaik</h6>
                    @if(count($ranking) > 0)
                    <div class="display-4 text-success mb-2">🥇</div>
                    <h4 class="text-success">{{ $ranking[0]['code'] }}</h4>
                    <h5>{{ $ranking[0]['name'] }}</h5>
                    <p class="text-muted mb-0">Skor: {{ number_format($ranking[0]['total'], 4) }}</p>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="10%" class="text-center">Rank</th>
                            <th width="15%">Kode</th>
                            <th width="30%">Nama Smartphone</th>
                            <th width="20%" class="text-center">Total Skor</th>
                            <th width="25%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ranking as $index => $alt)
                        <tr class="{{ $index == 0 ? 'table-success' : '' }}">
                            <td class="text-center font-weight-bold">
                                @if($index == 0)
                                <span class="badge badge-success p-2">🥇 Rank 1</span>
                                @elseif($index == 1)
                                <span class="badge badge-warning p-2">🥈 Rank 2</span>
                                @elseif($index == 2)
                                <span class="badge badge-danger p-2">🥉 Rank 3</span>
                                @else
                                <span class="badge badge-secondary">Rank {{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">{{ $alt['code'] }}</td>
                            <td>{{ $alt['name'] }}</td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($alt['total'], 4) }}
                            </td>
                            <td>
                                @if($index == 0)
                                <span class="badge badge-success p-2">REKOMENDASI TERBAIK</span>
                                @elseif($index < 3) <span class="badge badge-info p-2">REKOMENDASI</span>
                                    @else
                                    <span class="badge badge-light p-2">ALTERNATIF</span>
                                    @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Card -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="card-title text-primary">
                                Kesimpulan Analisis SAW
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><strong>Total Alternatif:</strong> {{ count($alternatives) }} smartphone
                                        </li>
                                        <li><strong>Total Kriteria:</strong> {{ count($criterias) }} kriteria</li>
                                        <li><strong>Metode:</strong> SAW + Bobot AHP</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    @if(count($ranking) > 0)
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">Rekomendasi Akhir:</h6>
                                        <p class="mb-0">
                                            Berdasarkan perhitungan SAW dengan bobot dari AHP,
                                            <strong>{{ $ranking[0]['name'] }} ({{ $ranking[0]['code'] }})</strong>
                                            merupakan pilihan terbaik dengan skor tertinggi
                                            <strong>{{ number_format($ranking[0]['total'], 4) }}</strong>.
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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

    .table th {
        vertical-align: middle !important;
        font-size: 0.9rem;
    }

    .table td {
        vertical-align: middle !important;
    }

    .progress {
        border-radius: 5px;
        overflow: hidden;
    }

    .badge {
        font-size: 0.85rem;
        padding: 5px 10px;
    }

    .display-4 {
        font-size: 3.5rem;
    }

</style>
@endsection
