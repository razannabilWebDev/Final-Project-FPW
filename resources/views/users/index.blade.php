@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#142d70]">Daftar Pengguna</h2>
            <p class="mt-1 text-xs text-slate-500">Kelola akun Pengelola dan Staf Operasional usaha Anda.</p>
        </div>
        @if (Route::has('users.store'))
            <button type="button" id="open-create-user"
                class="rounded-lg bg-[#2563eb] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1d4ed8]">
                + Tambah Pengguna
            </button>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#ecece8] bg-white shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        @forelse ($users as $user)
            @if ($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama</th>
                                <th class="px-5 py-3 font-semibold">Email</th>
                                <th class="px-5 py-3 font-semibold">Role</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f1f2]">
            @endif

            <tr class="hover:bg-slate-50/60">
                <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $user->name }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $user->email }}</td>
                <td class="px-5 py-3.5"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-[#1d4ed8]">{{ $user->role->label() }}</span></td>
                <td class="px-5 py-3.5">
                    @if ($user->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>Aktif</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-700"><span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>Nonaktif</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        @if (Route::has('users.edit'))
                            <a href="{{ route('users.edit', $user) }}" class="rounded-lg border border-[#dfe3e8] px-3 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-50">Ubah</a>
                        @endif
                        @if ($user->is(auth()->user()))
                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-400" title="Akun sendiri">Akun Anda</span>
                        @else
                            <form method="POST" action="{{ route('users.status', $user) }}"
                                onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan akun ini?' : 'Aktifkan kembali akun ini?' }}')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $user->is_active ? 1 : 0 }}">
                                <button type="submit"
                                    class="rounded-lg px-3 py-1.5 text-[11px] font-bold {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>

            @if ($loop->last)
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-[#f0f1f2] px-5 py-4">{{ $users->links() }}</div>
            @endif
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-600">Belum ada pengguna</p>
                <p class="mt-1 text-xs text-slate-400">Tambahkan akun Pengelola atau Staf Operasional pertama.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal: Tambah Pengguna (tetap di halaman yang sama) --}}
    @if (Route::has('users.store'))
        <x-modal id="create-user-modal" title="Tambah Pengguna"
            subtitle="Akun baru akan langsung aktif dan dapat digunakan untuk masuk."
            icon="plus" size="md">
            <form id="user-create-form" method="POST" action="{{ route('users.store') }}">
                @csrf
                @include('users._form', ['roles' => $roles, 'user' => null])
            </form>
            <x-slot:footer>
                <button type="button"
                    class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
                <button type="submit" form="user-create-form"
                    class="rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    Simpan Pengguna
                </button>
            </x-slot:footer>
        </x-modal>
    @endif

    <script>
        (function () {
            const openCreate = document.getElementById('open-create-user');
            const modal = document.getElementById('create-user-modal');

            if (openCreate && modal) {
                openCreate.addEventListener('click', function (event) {
                    event.preventDefault();
                    modal.openModal();
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.modal-root:not(.hidden)').forEach(function (root) {
                    root.closeModal();
                });
            });

            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function () {
                    if (modal) modal.openModal();
                });
            @endif
        })();
    </script>
@endsection
