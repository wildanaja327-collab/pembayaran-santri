<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['santri.program'])->withCount('santri');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'aktif' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'role' => 'required|in:admin,bendahara,wali_santri',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'no_telp' => $request->no_telp,
            'role' => $request->role,
            'is_active' => 1,
        ]);

        ActivityLog::record('USER_CREATE', "Menambahkan akun pengguna baru: '{$user->username}' sebagai role " . strtoupper($user->role));

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id_user . ',id_user',
            'nama_lengkap' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'role' => 'required|in:admin,bendahara,wali_santri',
        ]);

        $data = [
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'no_telp' => $request->no_telp,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::record('USER_UPDATE', "Memperbarui data akun pengguna: '{$user->username}' ({$user->nama_lengkap})");

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Jangan izinkan admin menonaktifkan dirinya sendiri
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang aktif digunakan.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record('USER_STATUS', "Akun '{$user->username}' telah {$statusStr}.");

        return redirect()->route('admin.users.index')->with('success', "Akun {$user->username} berhasil {$statusStr}.");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        ActivityLog::record('USER_RESET_PASS', "Mereset password untuk akun: '{$user->username}'");

        return redirect()->route('admin.users.index')->with('success', 'Password user ' . $user->username . ' berhasil direset.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Proteksi tidak bisa hapus diri sendiri
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->santri()->count() > 0) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih terhubung dengan data santri.');
        }

        $username = $user->username;
        $user->delete();

        ActivityLog::record('USER_DELETE', "Menghapus akun pengguna: '{$username}'");

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
