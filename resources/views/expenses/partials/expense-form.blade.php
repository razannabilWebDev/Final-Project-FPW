@php
    $isEdit = isset($expense) && $expense instanceof \App\Models\Expense;
@endphp

<form method="POST" action="{{ $isEdit ? route('expenses.update', $expense) : route('expenses.store') }}" id="expense-form" class="space-y-5">
    @csrf
    <input type="hidden" name="_method" value="{{ $isEdit ? 'PUT' : 'POST' }}">

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="expense_date" class="mb-2 block text-xs font-bold text-[#374151]">Tanggal</label>
            <input type="date" id="expense_date" name="expense_date"
                value="{{ old('expense_date', $isEdit ? $expense->expense_date->toDateString() : now()->toDateString()) }}" required
                class="h-11 w-full rounded-[10px] border border-line-input px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
        </div>
        <div>
            <label for="expense_category_id" class="mb-2 block text-xs font-bold text-[#374151]">Kategori</label>
            <select id="expense_category_id" name="expense_category_id" required
                class="h-11 w-full rounded-[10px] border border-line-input bg-white px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('expense_category_id', $isEdit ? $expense->expense_category_id : null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="amount" class="mb-2 block text-xs font-bold text-[#374151]">Nominal (Rp)</label>
        <input type="number" min="0" step="0.01" id="amount" name="amount"
            value="{{ old('amount', $isEdit ? (string) $expense->amount : '') }}" placeholder="0" required
            class="h-11 w-full rounded-[10px] border border-line-input px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
    </div>

    <div>
        <label for="description" class="mb-2 block text-xs font-bold text-[#374151]">Keterangan (opsional)</label>
        <textarea id="description" name="description" rows="3" placeholder="Contoh:_tagihan listrik Mei"
            class="w-full rounded-[10px] border border-line-input px-3 py-2.5 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">{{ old('description', $isEdit ? $expense->description : '') }}</textarea>
    </div>

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
