@php
    $isEdit = isset($category) && $category instanceof \App\Models\ExpenseCategory;
@endphp

<form method="POST" action="{{ $isEdit ? route('expense-categories.update', $category) : route('expense-categories.store') }}" id="category-form" class="space-y-5">
    @csrf
    <input type="hidden" name="_method" value="{{ $isEdit ? 'PUT' : 'POST' }}">

    <div>
        <label for="cat_name" class="mb-2 block text-xs font-bold text-[#374151]">Nama Kategori</label>
        <input type="text" id="cat_name" name="name" value="{{ old('name', $isEdit ? $category->name : '') }}" placeholder="Nama kategori" required
            class="h-11 w-full rounded-[10px] border border-line-input px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
    </div>

    <div>
        <label for="cat_description" class="mb-2 block text-xs font-bold text-[#374151]">Deskripsi (opsional)</label>
        <input type="text" id="cat_description" name="description" value="{{ old('description', $isEdit ? $category->description : '') }}" placeholder="Keterangan singkat"
            class="h-11 w-full rounded-[10px] border border-line-input px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
    </div>

    @if ($isEdit)
        <label class="flex items-center gap-2 text-xs text-[#4b5563]">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-blue-200">
            Aktif
        </label>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-danger-soft bg-[#fef2f2] px-4 py-3 text-xs text-[#b91c1c]">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>
