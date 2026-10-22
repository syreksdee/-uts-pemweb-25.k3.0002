<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class AuthController extends Controller
{
    protected ProfileService $profileService;
    protected UserRepositoryInterface $userRepo;

    public function __construct(ProfileService $profileService, UserRepositoryInterface $userRepo)
    {
        $this->profileService = $profileService;
        $this->userRepo = $userRepo;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'NIM / NIK / Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        $user = $this->userRepo->findByNimOrEmail($login);

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withInput($request->only('login'))->with('error', 'Login gagal: NIM/Email atau password salah.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Demo Fast Login Helper for evaluation ease
     */
    public function quickLogin(string $role)
    {
        $identifier = match ($role) {
            'admin' => '25.K3.0002',
            'kasir' => 'KASIR-001',
            'gudang' => 'GDG-001',
            'pelanggan' => 'CUST-001',
            default => '25.K3.0002',
        };

        $user = $this->userRepo->findByNimOrEmail($identifier);
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirectBasedOnRole($user)->with('success', "Berhasil masuk sebagai demo role: {$user->role->nama_role} ({$user->nama_lengkap})");
        }

        return redirect()->route('login')->with('error', 'Akun demo tidak ditemukan.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'required|email|unique:t002_users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:25',
            'alamat' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
            'foto' => 'nullable|file|mimes:jpeg,jpg,png|max:2048', // Validasi: tipe jpg/png & max 2MB
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'foto.mimes' => 'Format file foto harus JPG atau PNG.',
            'foto.max' => 'Ukuran file foto maksimal adalah 2 MB (2048 KB).',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        try {
            $this->profileService->updateProfile($user, $request->all(), $request->file('foto'));
            return redirect()->route('profile.show')->with('success', 'Profil dan foto berhasil diperbarui!');
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    protected function redirectBasedOnRole(User $user)
    {
        $role = $user->role ? $user->role->kode_role : '';

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kasir' => redirect()->route('kasir.pos'),
            'gudang' => redirect()->route('gudang.barang.index'),
            'pelanggan' => redirect()->route('toko.index'),
            default => redirect()->route('toko.index'),
        };
    }
}
