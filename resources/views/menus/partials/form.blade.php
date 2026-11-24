@php
    // Mode harus diberikan eksplisit oleh pemanggil. Mengira mode dari isset($menu)
    // tidak aman: variabel $menu bisa bocor dari loop kartu di halaman index.
    $isEdit = $isEdit ?? false;

    $formId = $formId ?? ($isEdit ? 'edit-menu-form-element' : 'create-menu-form-element');
    $fieldPrefix = $fieldPrefix ?? ($isEdit ? 'edit' : 'create');
@endphp

<form id="{{ $formId }}" method="POST" action="{{ $isEdit ? route('menus.update', $menu) : route('menus.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Dipakai index untuk membuka kembali modal yang gagal divalidasi. --}}
    <input type="hidden" name="form_modal" value="{{ $isEdit ? 'edit-menu-modal' : 'create-menu-modal' }}">
    @if ($isEdit)
        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
    @endif

    @include('menus._form', ['fieldPrefix' => $fieldPrefix])
</form>
