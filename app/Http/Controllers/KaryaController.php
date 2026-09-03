<?php

namespace App\Http\Controllers;

use App\Models\Karya;
use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class KaryaController extends Controller
{
    private function getUserFromToken($request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return null;
            }

            $payload = JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algorithm', 'HS256')));
            return User::find($payload->id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $jenis = $request->query('jenis', '');
        $tahun = $request->query('tahun', '');

        $query = Karya::with('user:id,name,email,role')
            ->where('status', '!=', 'draft');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($jenis && $jenis !== 'semua') {
            $query->where('jenis', $jenis);
        }

        if ($tahun && $tahun !== 'semua') {
            $query->where('tahun', $tahun);
        }

        $karya = $query->orderBy('updated_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $karya->items(),
            'pagination' => [
                'current_page' => $karya->currentPage(),
                'total' => $karya->total(),
                'per_page' => $karya->perPage(),
                'last_page' => $karya->lastPage(),
            ],
        ]);
    }

    public function getByUser(Request $request)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Karya::with('user:id,name,email,role')->where('user_id', $user->id);

        if ($search) {
            $query->where('judul', 'like', "%$search%");
        }

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        $karya = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $karya,
        ]);
    }

    public function show($id)
    {
        $karya = Karya::with('user:id,name,email,role')->find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $karya,
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'nama' => 'nullable|string|max:255',
            'nip_nim' => 'nullable|string|max:50',
            'jenis' => 'required|in:Publikasi,Penelitian,Pengabdian,Prestasi,HKI,Artikel',
            'level' => 'required|in:International,National,Local',
            'pencapaian' => 'nullable|string|max:100',
            'tahun' => 'required|integer|min:2000|max:' . date('Y'),
            'deskripsi' => 'nullable|string',
            'file_path' => 'nullable|string',
            'file_pendukung_path' => 'nullable|string',
        ]);

        $karya = Karya::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'nama' => $validated['nama'] ?? null,
            'nip_nim' => $validated['nip_nim'] ?? null,
            'jenis' => $validated['jenis'],
            'level' => $validated['level'],
            'pencapaian' => $validated['pencapaian'] ?? null,
            'tahun' => $validated['tahun'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_path' => $validated['file_path'] ?? null,
            'file_pendukung_path' => $validated['file_pendukung_path'] ?? null,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil dibuat',
            'data' => $karya,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if ($karya->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'nama' => 'nullable|string|max:255',
            'nip_nim' => 'nullable|string|max:50',
            'jenis' => 'sometimes|in:Publikasi,Penelitian,Pengabdian,Prestasi,HKI,Artikel',
            'level' => 'sometimes|in:International,National,Local',
            'pencapaian' => 'nullable|string|max:100',
            'tahun' => 'sometimes|integer|min:2000|max:' . date('Y'),
            'deskripsi' => 'nullable|string',
            'file_path' => 'nullable|string',
            'file_pendukung_path' => 'nullable|string',
            'status' => 'sometimes|in:draft',
        ]);

        $updateData = [
            'judul' => $validated['judul'] ?? $karya->judul,
            'nama' => $validated['nama'] ?? $karya->nama,
            'nip_nim' => $validated['nip_nim'] ?? $karya->nip_nim,
            'jenis' => $validated['jenis'] ?? $karya->jenis,
            'level' => $validated['level'] ?? $karya->level,
            'pencapaian' => $validated['pencapaian'] ?? $karya->pencapaian,
            'tahun' => $validated['tahun'] ?? $karya->tahun,
            'deskripsi' => $validated['deskripsi'] ?? $karya->deskripsi,
        ];

        if (!empty($validated['file_path'])) {
            $updateData['file_path'] = $validated['file_path'];
        }

        if (!empty($validated['file_pendukung_path'])) {
            $updateData['file_pendukung_path'] = $validated['file_pendukung_path'];
        }

        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        $karya->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil diupdate',
            'data' => $karya,
        ]);
    }

    public function submit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if ($karya->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        if ($karya->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Karya sudah disubmit sebelumnya',
            ], 400);
        }

        $karya->update([
            'status' => 'submitted',
            'tanggal_submit' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil disubmit',
            'data' => $karya,
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if ($karya->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        // Admin can delete verified or rejected
        // User can only delete draft
        if ($user->role === 'admin') {
            if ($karya->status !== 'verified' && $karya->status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin hanya bisa menghapus karya verified atau rejected',
                ], 400);
            }
        } else {
            if ($karya->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya karya draft yang bisa dihapus',
                ], 400);
            }
        }

        $karya->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil dihapus',
        ]);
    }

    public function download(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if (!$karya->file_path) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak tersedia',
            ], 404);
        }

        $filePath = storage_path('app/public/' . $karya->file_path);

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan di server',
            ], 404);
        }

        return response()->download($filePath);
    }

    public function uploadFile(Request $request)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        try {
            $file = $request->file('file');

            // Upload ke Cloudinary jika credential tersedia (storage permanen)
            if (env('CLOUDINARY_CLOUD_NAME')) {
                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key' => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                ]);

                $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'karya',
                    'resource_type' => 'auto',
                ]);

                return response()->json([
                    'success' => true,
                    'file_path' => $result['secure_url'],
                    'url' => $result['secure_url'],
                ], 200);
            }

            // Fallback: simpan lokal (sementara, hilang saat restart)
            $filePath = $file->store('karya', 'public');

            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'url' => asset('storage/' . $filePath),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(Request $request)
    {
        $tahun = $request->query('tahun');

        $jenisList = ['Publikasi', 'Penelitian', 'Pengabdian', 'Prestasi', 'HKI', 'Artikel'];
        $stats = [];

        foreach ($jenisList as $jenis) {
            $query = Karya::where('jenis', $jenis)->where('status', 'verified');

            if ($tahun) {
                $query->where('tahun', $tahun);
            }

            $stats[strtolower($jenis)] = $query->count();
        }

        $chartData = [];
        foreach ($stats as $key => $value) {
            $chartData[] = [
                'name' => ucfirst($key),
                'value' => $value,
            ];
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chart_data' => $chartData,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if ($karya->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya karya dengan status submitted yang bisa disetujui',
            ], 400);
        }

        $karya->update([
            'status' => 'verified',
        ]);

        $karya->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil disetujui',
            'data' => $karya,
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $karya = Karya::find($id);

        if (!$karya) {
            return response()->json([
                'success' => false,
                'message' => 'Karya tidak ditemukan',
            ], 404);
        }

        if ($karya->status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya karya dengan status submitted yang bisa ditolak',
            ], 400);
        }

        $validated = $request->validate([
            'alasan_reject' => 'required|string|max:500',
        ]);

        $karya->update([
            'status' => 'rejected',
            'alasan_reject' => $validated['alasan_reject'],
        ]);

        $karya->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Karya berhasil ditolak',
            'data' => $karya,
        ]);
    }
}
