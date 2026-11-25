<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index(): View
    {
        $users = User::query()->latest()->paginate(10);

        return view('users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function create(): View
    {
        return view('users.create', ['roles' => UserRole::cases()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->users->create($request->validated(), $actor);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->users->update($user, $actor, $request->validated());

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function setActive(Request $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $isActive = ! $request->boolean('is_active');
        $this->users->setActive($user, $actor, $isActive);

        $message = $isActive
            ? 'Akun pengguna berhasil diaktifkan.'
            : 'Akun pengguna berhasil dinonaktifkan.';

        return redirect()->route('users.index')->with('success', $message);
    }
}
