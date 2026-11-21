<?php

namespace App\Http\Controllers;

use App\Exceptions\MenuPhotoUploadException;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Services\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __construct(private readonly MenuService $menus) {}

    public function index(): View
    {
        $menus = Menu::query()->latest()->paginate(10);

        return view('menus.index', ['menus' => $menus]);
    }

    public function create(): View
    {
        return view('menus.create');
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        try {
            /** @var array{name: string, description?: string|null, selling_price: int|float|string} $data */
            $data = $request->safe()->except(['image']);
            $this->menus->create($data, $request->file('image'));
        } catch (MenuPhotoUploadException) {
            return redirect()->back()->withInput()->with('error', 'Foto menu gagal diunggah. Silakan coba lagi.');
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu): View
    {
        return view('menus.show', ['menu' => $menu]);
    }

    public function edit(Menu $menu): View
    {
        return view('menus.edit', ['menu' => $menu]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        try {
            /** @var array{name: string, description?: string|null, selling_price: int|float|string} $data */
            $data = $request->safe()->except(['image']);
            $this->menus->update($menu, $data, $request->file('image'));
        } catch (MenuPhotoUploadException) {
            return redirect()->back()->withInput()->with('error', 'Foto menu gagal diunggah. Silakan coba lagi.');
        }

        return redirect()->route('menus.index')->with('success', 'Data menu berhasil diperbarui.');
    }

    public function setActive(Request $request, Menu $menu): RedirectResponse
    {
        // Nilai `is_active` form adalah status saat ini; service membaliknya.
        $isActive = ! $request->boolean('is_active');
        $this->menus->setActive($menu, $isActive);

        $message = $isActive
            ? 'Menu berhasil diaktifkan.'
            : 'Menu berhasil dinonaktifkan.';

        return redirect()->route('menus.index')->with('success', $message);
    }
}
