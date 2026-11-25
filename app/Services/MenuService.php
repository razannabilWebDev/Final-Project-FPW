<?php

namespace App\Services;

use App\Exceptions\MenuPhotoUploadException;
use App\Models\Menu;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    private const PHOTO_DISK = 'public';

    private const PHOTO_DIRECTORY = 'menus';

    /**
     * @param  array{name: string, description?: string|null, selling_price: int|float|string}  $data
     */
    public function create(array $data, ?UploadedFile $photo = null): Menu
    {
        if ($photo !== null) {
            $data['image_path'] = $this->storePhoto($photo);
        }

        return Menu::create($data);
    }

    /**
     * @param  array{name: string, description?: string|null, selling_price: int|float|string}  $data
     */
    public function update(Menu $menu, array $data, ?UploadedFile $photo = null): Menu
    {
        $previousPath = $menu->image_path;

        if ($photo !== null) {
            $data['image_path'] = $this->storePhoto($photo);
        }

        $menu->update($data);

        if ($photo !== null && $previousPath !== null && $previousPath !== $menu->image_path) {
            Storage::disk(self::PHOTO_DISK)->delete($previousPath);
        }

        return $menu;
    }

    public function setActive(Menu $menu, bool $isActive): Menu
    {
        $menu->is_active = $isActive;
        $menu->save();

        return $menu;
    }

    private function storePhoto(UploadedFile $photo): string
    {
        $path = $photo->store(self::PHOTO_DIRECTORY, self::PHOTO_DISK);

        if ($path === false) {
            throw new MenuPhotoUploadException;
        }

        return $path;
    }
}
