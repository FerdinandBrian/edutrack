@extends(auth()->user()->id_role == 2 ? 'layouts.dosen' : (auth()->user()->id_role == 3 ? 'layouts.mahasiswa' : 'layouts.admin'))

@section('title','Edit Presensi')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h2 class="text-lg font-semibold">Edit Presensi</h2>

    @if($errors->any())
        <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="/presensi/{{ $presensi->id }}" method="POST" class="mt-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-slate-700 mb-1">NRP</label>
            <select name="nrp" required class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Mahasiswa --</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->nrp }}" {{ $m->nrp == $presensi->nrp ? 'selected' : '' }}>{{ $m->nrp }} — {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Jadwal (opsional)</label>
            <select name="jadwal_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Jadwal --</option>
                @foreach($jadwals as $j)
                    <option value="{{ $j->id }}" {{ $j->id == $presensi->jadwal_id ? 'selected' : '' }}>{{ $j->hari }} - {{ $j->waktu ?? '' }} - {{ $j->mata_kuliah ?? '' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $presensi->tanggal }}" required class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Status</label>
            <select name="status" required class="w-full border rounded px-3 py-2">
                <option value="Hadir" {{ $presensi->status=='Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Absen" {{ $presensi->status=='Absen' ? 'selected' : '' }}>Absen</option>
                <option value="Izin" {{ $presensi->status=='Izin' ? 'selected' : '' }}>Izin</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-slate-700 mb-1">Keterangan</label>
            <input type="text" name="keterangan" value="{{ $presensi->keterangan }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Perbarui</button>
            <a href="/presensi" class="ml-3 text-sm text-slate-600">Batal</a>
        </div>
    </form>
</div>
@endsection
