@php
    $isEdit = $isEdit ?? false;
    $formId = $formId ?? ($isEdit ? 'edit-recipe-form-element' : 'create-recipe-form-element');
    $prefix = $prefix ?? ($isEdit ? 'edit' : 'create');
@endphp

<form id="{{ $formId }}" method="POST"
      action="{{ $isEdit ? route('recipes.update', $recipe) : route('recipes.store') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Dipakai index untuk membuka kembali modal yang gagal divalidasi. --}}
    <input type="hidden" name="form_modal" value="{{ $isEdit ? 'edit-recipe-modal' : 'create-recipe-modal' }}">
    @if ($isEdit)
        <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
    @endif

    @include('recipes._form', ['isEdit' => $isEdit, 'prefix' => $prefix])
</form>
