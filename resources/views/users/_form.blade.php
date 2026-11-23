@php
    $isEdit = isset($user);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="mb-2 block text-xs font-bold text-slate-700">Nama Lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" autocomplete="name" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('name') border-red-400 @enderror">
        @error('name')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="email" class="mb-2 block text-xs font-bold text-slate-700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" autocomplete="email" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('email') border-red-400 @enderror">
        @error('email')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="role" class="mb-2 block text-xs font-bold text-slate-700">Role</label>
        <select id="role" name="role" required
            class="w-full rounded-lg border border-[#dfe3e8] bg-white px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('role') border-red-400 @enderror">
            @foreach ($roles as $roleOption)
                <option value="{{ $roleOption->value }}" @selected(old('role', $user->role?->value ?? 'staf') === $roleOption->value)>{{ $roleOption->label() }}</option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-2 block text-xs font-bold text-slate-700">Kata Sandi {{ $isEdit ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input id="password" name="password" type="password" autocomplete="new-password" @required(! $isEdit)
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('password') border-red-400 @enderror">
        @error('password')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="password_confirmation" class="mb-2 block text-xs font-bold text-slate-700">Konfirmasi Kata Sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
    </div>
</div>
