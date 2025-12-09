@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-body">
        <h6 class="card-title">Daftar Satuan</h6>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <form method="GET" action="{{ route('units.index') }}" class="d-flex w-50">
                <input type="text" name="search" class="form-control w-100" placeholder="Cari nama satuan..."
                    value="{{ $search ?? '' }}">
                <button class="btn btn-primary ml-2" type="submit" title="Cari">
                    <i data-feather="search"></i>
                </button>
            </form>

            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">
                Tambah Satuan
            </button>
        </div>


        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Dibuat Pada</th>
                        <th>Diubah Pada</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($units as $unit)
                    <tr>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->created_at }}</td>
                        <td>{{ $unit->updated_at }}</td>
                        <td class="text-right">

                            <button class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalEdit{{ $unit->uuid }}">
                                <i data-feather="edit-2"></i>
                            </button>

                            <form action="{{ route('units.destroy', $unit->uuid) }}" method="POST" data-confirm-delete
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </form>

                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $unit->uuid }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('units.update', $unit->uuid) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Satuan</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $unit->name }}" required>
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
    {{ $units->links('layouts.pagination') }}
</div>


    </div>
</div>

<div class="modal fade" id="modalCreate">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('units.store') }}">
            @csrf
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Satuan</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required placeholder="Masukkan nama satuan">
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
