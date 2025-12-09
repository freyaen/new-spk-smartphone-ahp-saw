@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-body">
        <h6 class="card-title">Daftar Kriteria</h6>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="GET" action="{{ route('criterias.index') }}" class="d-flex w-50">
                <input type="text" name="search" class="form-control w-100"
                    placeholder="Cari kriteria....." value="{{ $search ?? '' }}">
                <button class="btn btn-primary ml-2" type="submit" title="Cari">
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
                        <th>Tipe</th>
                        <th>Bobot AHP</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($criterias as $criteria)
                    <tr>
                        <td>{{ $criteria->code }}</td>
                        <td>{{ $criteria->name }}</td>
                        <td>{{ ucfirst($criteria->type) }}</td>
                        <td>{{ $criteria->value }}</td>

                        <td class="text-right">

                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalEdit{{ $criteria->uuid }}">
                                <i data-feather="edit-2"></i>
                            </button>

                            <!-- Delete -->
                            <form action="{{ route('criterias.destroy', $criteria->uuid) }}" method="POST"
                                data-confirm-delete class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </form>

                        </td>
                    </tr>

                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="modalEdit{{ $criteria->uuid }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('criterias.update', $criteria->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kriteria</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="form-group">
                                            <label>Code</label>
                                            <input type="text" name="code" class="form-control"
                                                value="{{ $criteria->code }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $criteria->name }}" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Tipe</label>
                                            <select name="type" class="form-control" required>
                                                <option value="benefit"
                                                    {{ $criteria->type == 'benefit' ? 'selected' : '' }}>Benefit
                                                </option>
                                                <option value="cost" {{ $criteria->type == 'cost' ? 'selected' : '' }}>
                                                    Cost</option>
                                            </select>
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
            {{ $criterias->links('layouts.pagination') }}
        </div>


    </div>
</div>

<!-- MODAL CREATE -->
<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('criterias.store') }}">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" required
                            placeholder="Masukkan kode kriteria">
                    </div>

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="Masukkan nama kriteria">
                    </div>

                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="type" class="form-control" required>
                            <option value="benefit">Benefit</option>
                            <option value="cost">Cost</option>
                        </select>
                    </div>

                    <!-- value AHP default 0 -->
                    <input type="hidden" name="value" value="0">

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection
