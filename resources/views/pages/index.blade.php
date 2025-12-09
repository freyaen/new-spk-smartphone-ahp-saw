@extends('layouts.app')

@section('content')
{{-- Tabel Kriteria Terbaru --}}
<div class="card mt-3">
    <div class="card-body">
        <h6 class="card-title">Kriteria Terbaru</h6>
        <div class="table-responsive">
            <table class="table table-datatable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Dibuat Pada</th>
                        <th>Diubah Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestCriterias as $criteria)
                    <tr>
                        <td>{{ $criteria->code }}</td>
                        <td>{{ $criteria->name }}</td>
                        <td>{{ ucfirst($criteria->type) }}</td>
                        <td>{{ $criteria->created_at }}</td>
                        <td>{{ $criteria->updated_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tabel Alternatif Terbaru --}}
<div class="card mt-3">
    <div class="card-body">
        <h6 class="card-title">Alternatif Terbaru</h6>
        <div class="table-responsive">
            <table class="table table-datatable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Dibuat Pada</th>
                        <th>Diubah Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestAlternatives as $alternative)
                    <tr>
                        <td>{{ $alternative->name }}</td>
                        <td>{{ $alternative->created_at }}</td>
                        <td>{{ $alternative->updated_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
