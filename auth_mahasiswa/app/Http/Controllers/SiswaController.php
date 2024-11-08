<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class SiswaController extends Controller
{
    // Menampilkan semua siswa
    public function index()
    {
        try {
            return Siswa::all();
        } catch (\Exception $e) {
            Log::error('Error fetching siswa data: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data siswa.'], 500);
        }
    }

    // Menyimpan data siswa baru
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'nama' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
                'kelas' => 'required|string|regex:/^[XIV]+\s+(IPA|IPS)\s+\d+$/|max:10',
                'umur' => 'required|integer|between:6,18',
            ]);

            // Proses penyimpanan data
            $siswa = Siswa::create($validatedData);
            return response()->json($siswa, 201);
        } catch (ValidationException $e) {
            // Menangani kesalahan validasi
            return response()->json([
                'error' => 'Validasi gagal.',
                'messages' => $e->errors(), // Mengembalikan pesan error dari validasi
            ], 422); // HTTP status code 422: Unprocessable Entity
        } catch (\Exception $e) {
            // Menangani kesalahan lain
            Log::error('Error storing siswa data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan data siswa.',
                'message' => $e->getMessage(), // Menyertakan pesan error yang lebih rinci
            ], 500); // HTTP status code 500: Internal Server Error
        }
    }


    // Menampilkan data siswa berdasarkan ID
    public function show($id)
    {
        try {
            return Siswa::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Error fetching siswa data by id: ' . $e->getMessage());
            return response()->json(['error' => 'Siswa tidak ditemukan.'], 404);
        }
    }

    // Memperbarui data siswa
    public function update(Request $request, $id)
    {
        try {
            $siswa = Siswa::findOrFail($id); // Mengambil data siswa berdasarkan ID

            // Validasi input
            $validatedData = $request->validate([
                'nama' => 'sometimes|required|string|regex:/^[a-zA-Z\s]+$/|max:255',
                'kelas' => 'sometimes|required|string|regex:/^[XIV]+\s+(IPA|IPS)\s+\d+$/|max:10',
                'umur' => 'sometimes|required|integer|between:6,18',
            ]);

            // Update data siswa
            $siswa->update($validatedData);
            return response()->json($siswa);
        } catch (ModelNotFoundException $e) {
            // Menangani jika siswa tidak ditemukan
            return response()->json([
                'error' => 'Siswa tidak ditemukan.',
                'message' => $e->getMessage(),
            ], 404); // HTTP status code 404: Not Found
        } catch (ValidationException $e) {
            // Menangani kesalahan validasi
            return response()->json([
                'error' => 'Validasi gagal.',
                'messages' => $e->errors(), // Mengembalikan pesan error dari validasi
            ], 422); // HTTP status code 422: Unprocessable Entity
        } catch (\Exception $e) {
            // Menangani kesalahan umum
            Log::error('Error updating siswa data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memperbarui data siswa.',
                'message' => $e->getMessage(), // Menyertakan pesan error yang lebih rinci
            ], 500); // HTTP status code 500: Internal Server Error
        }
    }


    // Menghapus data siswa
    public function destroy($id)
    {
        try {
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting siswa data: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus data siswa.'], 500);
        }
    }
}
