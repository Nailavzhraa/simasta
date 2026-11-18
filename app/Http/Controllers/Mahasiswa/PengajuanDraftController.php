<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PengajuanDraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengajuanDraftController extends Controller
{
    /**
     * Mengambil draft milik mahasiswa yang login.
     */
    public function show(Request $request): JsonResponse
    {
        $draft = PengajuanDraft::where(
            'user_id',
            $request->user()->id
        )->first();

        return response()->json([
            'success' => true,
            'draft' => $draft ? [
                'form_data' => $draft->form_data ?? [],
                'current_step' => $draft->current_step,
                'updated_at' => $draft->updated_at?->toISOString(),
            ] : null,
        ]);
    }

    /**
     * Menyimpan isian formulir secara bertahap.
     */
    public function save(Request $request): JsonResponse
    {
        $fields = [
            'nama',
            'nim',
            'email',
            'no_telepon',
            'no_wa',
            'perguruan_tinggi',
            'program_studi',
            'tahun_angkatan',
            'semester',
            'tanggal_mulai',
            'durasi_bulan',
            'jenis_pendaftaran',
            'link_cv',
            'link_surat',
            'link_proposal',
            'tujuan_magang',
            'keterangan_tambahan',
        ];

        $validated = $request->validate([
            'current_step' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'form_data' => [
                'required',
                'array:' . implode(',', $fields),
            ],

            'form_data.nama' => 'sometimes|nullable|string|max:255',
            'form_data.nim' => 'sometimes|nullable|string|max:255',
            'form_data.email' => 'sometimes|nullable|email|max:255',
            'form_data.no_telepon' => 'sometimes|nullable|string|max:20',
            'form_data.no_wa' => 'sometimes|nullable|string|max:20',

            'form_data.perguruan_tinggi' => 'sometimes|nullable|string|max:255',
            'form_data.program_studi' => 'sometimes|nullable|string|max:255',
            'form_data.tahun_angkatan' => 'sometimes|nullable|string|max:4',
            'form_data.semester' => 'sometimes|nullable|integer|between:1,14',

            'form_data.tanggal_mulai' => 'sometimes|nullable|date_format:Y-m-d',
            'form_data.durasi_bulan' => 'sometimes|nullable|integer|between:1,12',
            'form_data.jenis_pendaftaran' => 'sometimes|nullable|in:reguler,susulan',

            'form_data.link_cv' => 'sometimes|nullable|string|max:2048',
            'form_data.link_surat' => 'sometimes|nullable|string|max:2048',
            'form_data.link_proposal' => 'sometimes|nullable|string|max:2048',

            'form_data.tujuan_magang' => 'sometimes|nullable|string|max:3000',
            'form_data.keterangan_tambahan' => 'sometimes|nullable|string|max:3000',
        ]);

        $userId = $request->user()->id;

        $draft = DB::transaction(function () use (
            $userId,
            $validated
        ) {
            // Gunakan lock yang sama dengan proses
            // submit final DokumenController.
            DB::table('users')
                ->where('id', $userId)
                ->lockForUpdate()
                ->first();

            $aktif = Pengajuan::where('user_id', $userId)
                ->whereIn('status', [
                    'diproses',
                    'diverifikasi',
                    'diterima',
                ])
                ->exists();

            if ($aktif) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Pengajuan sudah dikirim '
                        . 'atau diterima. Draft tidak dapat diubah.',
                ]);
            }

            $draft = PengajuanDraft::firstOrNew([
                'user_id' => $userId,
            ]);

            $dataLama = $draft->form_data ?? [];
            $dataBaru = $validated['form_data'];

            // Gabungkan perubahan tanpa menghapus
            // isian tahap sebelumnya.
            $draft->form_data = array_merge(
                $dataLama,
                $dataBaru
            );

            $draft->current_step = $validated['current_step'];
            $draft->save();

            return $draft;
        });

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil disimpan.',
            'current_step' => $draft->current_step,
            'updated_at' => $draft->updated_at?->toISOString(),
        ]);
    }

    /**
     * Menghapus draft milik mahasiswa.
     */
    public function destroy(Request $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            $userId = $request->user()->id;

            DB::table('users')
                ->where('id', $userId)
                ->lockForUpdate()
                ->first();

            PengajuanDraft::where('user_id', $userId)
                ->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil dihapus.',
        ]);
    }
}
