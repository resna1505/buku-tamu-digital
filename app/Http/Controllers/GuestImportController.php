<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestGroup;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman import
     */
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        // Get groups untuk default group saat import
        $groups = GuestGroup::where('event_id', $event->id)->get();

        return view('guests.import', compact('event', 'groups'));
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header - sesuai dengan form Excel yang diupload
        $headers = [
            'A1' => 'Timestamp',
            'B1' => 'Fakultas',
            'C1' => 'Program Studi',
            'D1' => 'Nama Lengkap Mahasiswa/i',
            'E1' => 'Nama Lengkap Orang Tua/Saudara (Tamu 1)',
            'F1' => 'NPM',
            'G1' => 'No. Handphone ( WhatsApp Aktif )',
            'H1' => 'Nama Lengkap Orang Tua/Saudara (Tamu 2)',
            'I1' => 'Email',
            'J1' => 'Sertikan bukti pembayaran biaya wisuda'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
        }

        // Contoh data
        $sheet->setCellValue('A2', '2025-10-21 21:49:40');
        $sheet->setCellValue('B2', 'Teknik');
        $sheet->setCellValue('C2', 'S1 SISTEM INFORMASI');
        $sheet->setCellValue('D2', 'John Doe');
        $sheet->setCellValue('E2', 'Jane Doe (Ibu)');
        $sheet->setCellValue('F2', '123123123');
        $sheet->setCellValue('G2', '628123456789');
        $sheet->setCellValue('H2', 'Jack Doe (Ayah)');
        $sheet->setCellValue('I2', 'john@example.com');
        $sheet->setCellValue('J2', 'https://drive.google.com/...');

        // Auto size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $fileName = 'template_import_tamu_wisuda_' . date('Y-m-d') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Preview data dari Excel sebelum import
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Ambil header (baris pertama)
            $headers = array_shift($rows);

            // Proses data
            $data = [];
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                $rowData = [
                    'row_number' => $rowNumber,
                    'timestamp' => $row[0] ?? null,
                    'fakultas' => $row[1] ?? '',
                    'program_studi' => $row[2] ?? '',
                    'nama_mahasiswa' => $row[3] ?? '',
                    'tamu_1' => $row[4] ?? '',
                    'npm' => $row[5] ?? '',
                    'phone' => $row[6] ?? '',
                    'tamu_2' => $row[7] ?? '',
                    'email' => $row[8] ?? '',
                    'bukti_pembayaran' => $row[9] ?? '',
                    'is_valid' => true,
                    'validation_errors' => []
                ];

                // Validasi data
                $validator = Validator::make($rowData, [
                    'tamu_1' => 'required|string|max:255',
                    'phone' => 'required',
                ], [
                    'tamu_1.required' => 'Nama Tamu 1 wajib diisi',
                    'phone.required' => 'Nomor WhatsApp wajib diisi',
                ]);

                if ($validator->fails()) {
                    $rowData['is_valid'] = false;
                    $rowData['validation_errors'] = $validator->errors()->all();
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                }

                $data[] = $rowData;
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'total_rows' => count($data),
                'valid_rows' => count(array_filter($data, fn($item) => $item['is_valid'])),
                'invalid_rows' => count(array_filter($data, fn($item) => !$item['is_valid'])),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error membaca file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import data dari Excel ke database
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'default_group_id' => 'required|exists:guest_groups,id'
        ], [
            'default_group_id.required' => 'Silakan pilih grup tamu default untuk import',
        ]);

        try {
            $event = Event::where('is_active', true)->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada event aktif.'
                ], 400);
            }

            $file = $request->file('file');
            $defaultGroupId = $request->input('default_group_id');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Hapus header
            array_shift($rows);

            $imported = 0;
            $failed = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Format nomor WhatsApp
                    $phone = preg_replace('/\s+/', '', $row[6] ?? '');
                    if (substr($phone, 0, 1) === '0') {
                        $phone = '62' . substr($phone, 1);
                    } elseif (substr($phone, 0, 2) !== '62') {
                        $phone = '62' . $phone;
                    }

                    // Validasi Tamu 1 (wajib)
                    if (empty($row[4])) {
                        $failed++;
                        $errors[] = "Baris {$rowNumber}: Nama Tamu 1 tidak boleh kosong";
                        continue;
                    }

                    // Hitung jumlah tamu (1 atau 2)
                    $guestCount = !empty($row[7]) ? 2 : 1;

                    // Buat 1 RECORD untuk 1 mahasiswa dengan Tamu 1 & 2
                    $guestData = [
                        'event_id' => $event->id,
                        'name' => $row[3] ?? 'Mahasiswa', // Nama mahasiswa sebagai name utama
                        'student_name' => $row[3] ?? null,
                        'npm' => $row[5] ?? null,
                        'faculty' => $row[1] ?? null,
                        'study_program' => $row[2] ?? null,
                        'email' => $row[8] ?? null,
                        'whatsapp' => $phone,
                        'payment_proof' => $row[9] ?? null,
                        'registration_date' => $row[0] ?? now(),
                        'guest_1_name' => $row[4], // Tamu 1 (wajib)
                        'guest_2_name' => $row[7] ?? null, // Tamu 2 (opsional)
                        'address' => '-',
                        'is_vip' => false,
                        'group_id' => $defaultGroupId,
                        'is_invited' => true,
                        'guests_count' => $guestCount,
                        'qr_code' => 'GUEST-' . strtoupper(Str::random(10)),
                    ];

                    Guest::create($guestData);
                    $imported++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Baris {$rowNumber}: {$e->getMessage()}";
                }
            }

            DB::commit();

            $message = "Import berhasil! {$imported} tamu berhasil diimport";
            if ($failed > 0) {
                $message .= ", {$failed} gagal";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error saat import: ' . $e->getMessage()
            ], 500);
        }
    }
}
