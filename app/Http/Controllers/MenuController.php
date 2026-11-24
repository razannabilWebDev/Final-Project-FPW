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

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        $query = Menu::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status === 'aktif', fn ($query) => $query->where('is_active', true))
            ->when($status === 'nonaktif', fn ($query) => $query->where('is_active', false));

        $menus = (clone $query)->latest()->paginate(9)->withQueryString();

        $stats = Menu::query()
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as aktif, COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) as nonaktif')
            ->first();

        $menuStats = [
            'total' => (int) ($stats->total ?? 0),
            'aktif' => (int) ($stats->aktif ?? 0),
            'nonaktif' => (int) ($stats->nonaktif ?? 0),
        ];

        // Setelah validasi gagal, old input dipakai untuk membuka kembali modal
        // dengan form yang sudah berisi nilai dan pesan error.
        $failedMenu = null;
        if (session()->getOldInput('form_modal') === 'edit-menu-modal') {
            $failedMenu = Menu::find(session()->getOldInput('menu_id'));
        }

        return view('menus.index', compact('menus', 'menuStats', 'search', 'status', 'failedMenu'));
    }

    public function create(Request $request): View
    {
        if ($request->boolean('partial')) {
            return view('menus.partials.form', ['isEdit' => false]);
        }

        return view('menus.create');
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        try {
            /** @var array{name: string, description?: string|null, selling_price: int|float|string} $data */
            $data = $request->safe()->except(['image', 'form_modal', 'menu_id']);
            $this->menus->create($data, $request->file('image'));
        } catch (MenuPhotoUploadException) {
            return redirect()->back()->withInput()->with('error', 'Foto menu gagal diunggah. Silakan coba lagi.');
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Request $request, Menu $menu): View
    {
        if ($request->boolean('partial')) {
            return view('menus.partials.detail', compact('menu'));
        }

        return view('menus.show', ['menu' => $menu]);
    }

    public function edit(Request $request, Menu $menu): View
    {
        if ($request->boolean('partial')) {
            return view('menus.partials.form', ['isEdit' => true, 'menu' => $menu]);
        }

        return view('menus.edit', ['menu' => $menu]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        try {
            /** @var array{name: string, description?: string|null, selling_price: int|float|string} $data */
            $data = $request->safe()->except(['image', 'form_modal', 'menu_id']);
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
