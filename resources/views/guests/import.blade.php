@extends('layouts.app')

@section('title', 'Import Data Tamu')

@section('content')
<!-- Header -->
<div class="flex items-center mb-6">
    <a href="{{ route('guests.create') }}" class="mr-4 text-purple-600 hover:text-purple-800">
        <i class="fas fa-arrow-left text-2xl"></i>
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Import Data Tamu dari Excel</h2>
</div>

<!-- Steps Indicator -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col items-center flex-1" id="step1-indicator">
            <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold mb-2">
                1
            </div>
            <span class="text-sm font-semibold text-purple-600">Upload File</span>
        </div>
        <div class="flex-1 h-1 bg-gray-300 mx-2" id="line1"></div>
        <div class="flex flex-col items-center flex-1" id="step2-indicator">
            <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold mb-2">
                2
            </div>
            <span class="text-sm text-gray-600">Preview Data</span>
        </div>
        <div class="flex-1 h-1 bg-gray-300 mx-2" id="line2"></div>
        <div class="flex flex-col items-center flex-1" id="step3-indicator">
            <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold mb-2">
                3
            </div>
            <span class="text-sm text-gray-600">Hasil Import</span>
        </div>
    </div>
</div>

<!-- Step 1: Upload File -->
<div id="step1" class="bg-white rounded-2xl p-6 card-shadow">
    <h3 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload File Excel
    </h3>

    <!-- Info & Download Template -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="flex-1">
                <h4 class="font-semibold text-blue-900 mb-2">Perhatian Penting:</h4>
                <ul class="text-sm text-blue-800 space-y-1 mb-3">
                    <li>• Format file harus <strong>.xlsx</strong> atau <strong>.xls</strong></li>
                    <li>• Maksimal ukuran file <strong>5 MB</strong></li>
                    <li>• Pastikan format sesuai dengan template yang disediakan</li>
                    <li>• <strong>1 baris = 1 data mahasiswa</strong> dengan Tamu 1 & Tamu 2</li>
                </ul>
                <a href="{{ route('guests.import.template') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm">
                    <i class="fas fa-download mr-2"></i>Download Template Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Select Default Group -->
    <div class="mb-6">
        <label class="block text-gray-700 font-semibold mb-2">
            Grup Tamu Default <span class="text-red-500">*</span>
        </label>
        <select id="defaultGroupSelect"
            class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-purple-500">
            <option value="">Pilih Grup Tamu</option>
            @foreach($groups as $group)
                <option
                    value="{{ $group->id }}"
                    {{ $loop->first && old('group_id') == null ? 'selected' : '' }}
                >
                    {{ $group->name }}
                </option>
            @endforeach
        </select>

        <p class="text-sm text-gray-500 mt-1">
            <i class="fas fa-info-circle mr-1"></i>Semua tamu yang diimport akan masuk ke grup ini
        </p>
    </div>

    <!-- Upload Area -->
    <div id="uploadArea"
         class="border-2 border-dashed border-gray-300 rounded-xl p-12 text-center cursor-pointer hover:border-purple-500 hover:bg-purple-50 transition">
        <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
        <h4 class="text-xl font-semibold text-gray-700 mb-2">Drag & Drop File Excel</h4>
        <p class="text-gray-500 mb-4">atau</p>
        <button type="button"
                onclick="document.getElementById('fileInput').click()"
                class="btn-primary px-6 py-3 rounded-xl font-semibold">
            <i class="fas fa-folder-open mr-2"></i>Pilih File
        </button>
        <input type="file" id="fileInput" accept=".xlsx,.xls" class="hidden">
    </div>

    <!-- File Info (Hidden Initially) -->
    <div id="fileInfo" class="hidden mt-6">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-excel text-green-600 text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold text-green-900">File dipilih:</p>
                    <p class="text-sm text-green-700" id="fileName"></p>
                </div>
            </div>
            <button type="button"
                    onclick="clearFile()"
                    class="text-red-600 hover:text-red-800">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <button type="button"
                id="previewBtn"
                class="w-full mt-4 btn-primary py-4 rounded-xl font-semibold text-lg">
            <i class="fas fa-eye mr-2"></i>Preview Data
        </button>
    </div>
</div>

<!-- Step 2: Preview (Hidden Initially) -->
<div id="step2" class="hidden bg-white rounded-2xl p-6 card-shadow">
    <h3 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-eye mr-2"></i>Preview Data
    </h3>

    <!-- Summary Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-blue-600" id="totalRows">0</div>
            <div class="text-sm text-gray-600 mt-1">Total Baris</div>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-green-600" id="validRows">0</div>
            <div class="text-sm text-gray-600 mt-1">Data Valid</div>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-red-600" id="invalidRows">0</div>
            <div class="text-sm text-gray-600 mt-1">Data Tidak Valid</div>
        </div>
    </div>

    <!-- Errors Container -->
    <div id="errorsContainer" class="hidden mb-6">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <h4 class="font-semibold text-red-900 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>Error Validasi:
            </h4>
            <ul id="errorsList" class="text-sm text-red-700 space-y-1"></ul>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="overflow-x-auto border border-gray-200 rounded-xl mb-6" style="max-height: 400px;">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Nama Mahasiswa</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">NPM</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Fakultas</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tamu 1</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Tamu 2</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">WhatsApp</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody id="previewTableBody" class="divide-y divide-gray-200">
            </tbody>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <button type="button"
                onclick="backToStep1()"
                class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </button>
        <button type="button"
                id="importBtn"
                class="flex-1 bg-green-500 text-white py-4 rounded-xl font-semibold text-lg hover:bg-green-600 transition">
            <i class="fas fa-upload mr-2"></i>Import Data
        </button>
    </div>
</div>

<!-- Step 3: Result (Hidden Initially) -->
<div id="step3" class="hidden bg-white rounded-2xl p-6 card-shadow">
    <div id="importResult"></div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-purple-600 mx-auto mb-4"></div>
        <p class="text-lg font-semibold text-gray-700">Memproses data...</p>
    </div>
</div>

@push('scripts')
<script>
let selectedFile = null;
let previewData = null;

// Setup CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Upload Area Events
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('border-purple-500', 'bg-purple-50');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('border-purple-500', 'bg-purple-50');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('border-purple-500', 'bg-purple-50');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFileSelect(files[0]);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    // Validasi tipe file
    const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'];
    if (!validTypes.includes(file.type)) {
        alert('File harus berformat .xlsx atau .xls');
        return;
    }

    // Validasi ukuran file (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5 MB');
        return;
    }

    selectedFile = file;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileInfo').classList.remove('hidden');
}

function clearFile() {
    selectedFile = null;
    fileInput.value = '';
    document.getElementById('fileInfo').classList.add('hidden');
}

// Preview Button
document.getElementById('previewBtn').addEventListener('click', async () => {
    if (!selectedFile) {
        alert('Pilih file Excel terlebih dahulu');
        return;
    }

    const formData = new FormData();
    formData.append('file', selectedFile);

    showLoading();

    try {
        const response = await fetch('{{ route("guests.import.preview") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });

        hideLoading(); // HIDE DULU SEBELUM CEK RESPONSE

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            previewData = result.data;
            displayPreview(result);
            goToStep2();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message + '\n\nCek console browser (F12) untuk detail error.');
    }
});

function displayPreview(result) {
    // Update statistics
    document.getElementById('totalRows').textContent = result.total_rows;
    document.getElementById('validRows').textContent = result.valid_rows;
    document.getElementById('invalidRows').textContent = result.invalid_rows;

    // Show errors if any
    if (result.errors && result.errors.length > 0) {
        document.getElementById('errorsContainer').classList.remove('hidden');
        const errorsList = document.getElementById('errorsList');
        errorsList.innerHTML = result.errors.map(err => `<li>• ${err}</li>`).join('');
    }

    // Populate table
    const tbody = document.getElementById('previewTableBody');
    tbody.innerHTML = result.data.map(row => {
        const statusClass = row.is_valid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = row.is_valid ? 'Valid' : 'Error';
        const statusIcon = row.is_valid ? 'fa-check-circle' : 'fa-times-circle';

        return `
            <tr class="${row.is_valid ? '' : 'bg-red-50'}">
                <td class="px-4 py-3">${row.row_number}</td>
                <td class="px-4 py-3">${row.nama_mahasiswa || '-'}</td>
                <td class="px-4 py-3">${row.npm || '-'}</td>
                <td class="px-4 py-3">${row.fakultas || '-'}</td>
                <td class="px-4 py-3 font-semibold">${row.tamu_1 || '-'}</td>
                <td class="px-4 py-3">${row.tamu_2 || '-'}</td>
                <td class="px-4 py-3">${row.phone || '-'}</td>
                <td class="px-4 py-3">
                    <span class="${statusClass} px-2 py-1 rounded-full text-xs font-semibold">
                        <i class="fas ${statusIcon} mr-1"></i>${statusText}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

// Import Button
document.getElementById('importBtn').addEventListener('click', async () => {
    const groupId = document.getElementById('defaultGroupSelect').value;

    if (!groupId) {
        alert('Pilih grup tamu default terlebih dahulu');
        return;
    }

    if (!selectedFile) {
        alert('File tidak ditemukan');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin mengimport data ini?')) {
        return;
    }

    const formData = new FormData();
    formData.append('file', selectedFile);
    formData.append('default_group_id', groupId);

    showLoading();

    try {
        const response = await fetch('{{ route("guests.import.process") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });

        hideLoading(); // HIDE DULU SEBELUM CEK RESPONSE

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        displayImportResult(result);
        goToStep3();

    } catch (error) {
        hideLoading();
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message + '\n\nCek console browser (F12) untuk detail error.');
    }
});

function displayImportResult(result) {
    const resultDiv = document.getElementById('importResult');

    if (result.success) {
        let html = `
            <div class="text-center mb-6">
                <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Import Berhasil!</h3>
                <p class="text-gray-600">${result.message}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold text-green-600">${result.imported}</div>
                    <div class="text-sm text-gray-600 mt-1">Berhasil Diimport</div>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <div class="text-3xl font-bold text-red-600">${result.failed}</div>
                    <div class="text-sm text-gray-600 mt-1">Gagal</div>
                </div>
            </div>
        `;

        if (result.errors && result.errors.length > 0) {
            html += `
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-yellow-900 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error Details:
                    </h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        ${result.errors.map(err => `<li>• ${err}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        html += `
            <div class="flex space-x-3">
                <a href="{{ route('guests.index') }}"
                   class="flex-1 btn-primary text-center py-4 rounded-xl font-semibold text-lg">
                    <i class="fas fa-list mr-2"></i>Lihat Daftar Tamu
                </a>
                <button type="button"
                        onclick="location.reload()"
                        class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-lg hover:bg-gray-400 transition">
                    <i class="fas fa-redo mr-2"></i>Import Lagi
                </button>
            </div>
        `;

        resultDiv.innerHTML = html;
    } else {
        resultDiv.innerHTML = `
            <div class="text-center mb-6">
                <i class="fas fa-times-circle text-red-500 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Import Gagal!</h3>
                <p class="text-gray-600">${result.message}</p>
            </div>

            <button type="button"
                    onclick="backToStep1()"
                    class="w-full bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-lg hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i>Coba Lagi
            </button>
        `;
    }
}

// Step Navigation
function goToStep2() {
    updateStepIndicator(2);
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToStep3() {
    updateStepIndicator(3);
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function backToStep1() {
    updateStepIndicator(1);
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('errorsContainer').classList.add('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateStepIndicator(step) {
    // Reset all
    for (let i = 1; i <= 3; i++) {
        const indicator = document.getElementById(`step${i}-indicator`);
        const circle = indicator.querySelector('div');
        const text = indicator.querySelector('span');

        if (i < step) {
            circle.className = 'w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center font-bold mb-2';
            text.className = 'text-sm font-semibold text-green-600';
        } else if (i === step) {
            circle.className = 'w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold mb-2';
            text.className = 'text-sm font-semibold text-purple-600';
        } else {
            circle.className = 'w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold mb-2';
            text.className = 'text-sm text-gray-600';
        }
    }

    // Update lines
    document.getElementById('line1').className = step > 1 ? 'flex-1 h-1 bg-green-500 mx-2' : 'flex-1 h-1 bg-gray-300 mx-2';
    document.getElementById('line2').className = step > 2 ? 'flex-1 h-1 bg-green-500 mx-2' : 'flex-1 h-1 bg-gray-300 mx-2';
}

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Auto hide loading on page load
document.addEventListener('DOMContentLoaded', function() {
    hideLoading();
    console.log('Import page loaded successfully!');
});
</script>
@endpush
@endsection
