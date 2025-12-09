@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-body">
        <h6 class="card-title">Daftar Alternatif</h6>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="GET" action="{{ route('alternatives.index') }}" class="d-flex w-50">
                <input type="text" name="search" class="form-control w-100"
                    placeholder="Cari smartphone....." value="{{ $search ?? '' }}">
                <button class="btn btn-primary ml-2" type="submit">
                    <i data-feather="search"></i>
                </button>
            </form>

            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
                Tambah
            </button>
        </div>


        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nama</th>
                        @foreach ($criterias as $criteria)
                        <th>{{ $criteria->code }} - {{ $criteria->name }}</th>
                        @endforeach
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($alternatives as $alt)
                    <tr>
                        <td>{{ $alt->code }}</td>
                        <td>{{ $alt->name }}</td>
                        @foreach ($criterias as $criteria)
                        @php
                        $altValue = $alt->criterias()->where('criteria_uuid', $criteria->uuid)->first();
                        @endphp
                        <td>{{ $altValue ? number_format($altValue->value, 2) : 0.00 }}</td>
                        @endforeach
                        <td class="text-right">

                            <button class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalEdit{{ $alt->uuid }}">
                                <i data-feather="edit-2"></i>
                            </button>

                            <form action="{{ route('alternatives.destroy', $alt->uuid) }}" method="POST"
                                data-confirm-delete class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </form>

                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    <div class="modal fade" id="modalEdit{{ $alt->uuid }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('alternatives.update', $alt->uuid) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Alternatif</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Code</label>
                                            <input type="text" name="code" class="form-control" value="{{ $alt->code }}"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" name="name" class="form-control" value="{{ $alt->name }}"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label>Nilai Kriteria</label>
                                            @foreach ($criterias as $criteria)
                                            @php
                                            $altValue = $alt->criterias()->where('criteria_uuid',
                                            $criteria->uuid)->first();
                                            @endphp
                                            <div class="mb-2">
                                                <label>{{ $criteria->code }} - {{ $criteria->name }}</label>
                                                <input type="number" step="any" class="form-control"
                                                    name="criteria[{{ $criteria->uuid }}]" required
                                                    value="{{ $altValue ? number_format($altValue->value , 2): 0.00 }}">
                                            </div>
                                            @endforeach
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Simpan</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    @endforeach
                </tbody>

            </table>


        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $alternatives->links('layouts.pagination') }}
        </div>

    </div>
</div>

{{-- Modal Create --}}
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('alternatives.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Alternatif</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" required
                            placeholder="Masukkan code alternatif">
                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="Masukkan nama alternatif">
                    </div>

                    <div class="form-group">
                        <label>Nilai Kriteria</label>
                        @foreach ($criterias as $criteria)
                        <div class="mb-2">
                            <label>{{ $criteria->code }} - {{ $criteria->name }}</label>
                            <input type="number" step="any" class="form-control" name="criteria[{{ $criteria->uuid }}]"
                                required value="0">
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection
