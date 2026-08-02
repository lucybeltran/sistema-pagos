<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class BackupController extends Controller
{
    protected $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
    }

    public function index()
    {
        // Ensure backups directory exists
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }

        // List files in the directory
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'zip') {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'size' => $this->formatBytes($file->getSize()),
                    'raw_size' => $file->getSize(),
                ];
            }
        }

        // Sort backups by modification date descending
        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        // Load settings
        $settingsPath = storage_path('app/backup_settings.json');
        $settings = [
            'activo' => false,
            'frecuencia' => 'mensual',
            'hora' => '23:30',
        ];

        if (File::exists($settingsPath)) {
            $settings = array_merge($settings, json_decode(File::get($settingsPath), true) ?: []);
        }

        return view('backups.index', compact('backups', 'settings'));
    }

    public function store()
    {
        try {
            if (!File::exists($this->backupDir)) {
                File::makeDirectory($this->backupDir, 0755, true);
            }

            $dbPath = database_path('database.sqlite');
            if (!File::exists($dbPath)) {
                return redirect()->back()->with('error', 'La base de datos SQLite no existe.');
            }

            $filename = 'respaldo_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = $this->backupDir . '/' . $filename;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                // Add the database sqlite file
                $zip->addFile($dbPath, 'database.sqlite');
                $zip->close();

                return redirect()->route('backups.index')->with('success', 'Respaldo generado correctamente.');
            } else {
                return redirect()->back()->with('error', 'No se pudo crear el archivo ZIP.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar respaldo: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filePath = $this->backupDir . '/' . $filename;

        if (!File::exists($filePath) || strpos($filename, '..') !== false) {
            abort(404, 'Archivo no encontrado');
        }

        return Response::download($filePath);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'nullable|file|mimes:zip',
            'backup_file_name' => 'nullable|string',
        ]);

        try {
            $zipRealPath = null;
            if ($request->hasFile('backup_file')) {
                $zipRealPath = $request->file('backup_file')->getRealPath();
            } elseif ($request->has('backup_file_name')) {
                $filename = $request->input('backup_file_name');
                if (strpos($filename, '..') !== false) {
                    return redirect()->back()->with('error', 'Nombre de archivo inválido.');
                }
                $zipRealPath = $this->backupDir . '/' . $filename;
                if (!File::exists($zipRealPath)) {
                    return redirect()->back()->with('error', 'Archivo de respaldo no encontrado.');
                }
            } else {
                return redirect()->back()->with('error', 'Debes subir un archivo o seleccionar uno de la lista.');
            }

            $tempExtractPath = storage_path('app/temp_restore_' . time());

            // Backup current database in case of failure
            $dbPath = database_path('database.sqlite');
            $dbBackupPath = database_path('database.sqlite.bak');
            
            if (File::exists($dbPath)) {
                File::copy($dbPath, $dbBackupPath);
            }

            // Extract ZIP file
            $zip = new ZipArchive();
            if ($zip->open($zipRealPath) === true) {
                $zip->extractTo($tempExtractPath);
                $zip->close();

                $extractedDb = $tempExtractPath . '/database.sqlite';

                if (File::exists($extractedDb)) {
                    // Overwrite the sqlite database
                    File::copy($extractedDb, $dbPath);
                    
                    // Clean up temp files
                    File::deleteDirectory($tempExtractPath);
                    if (File::exists($dbBackupPath)) {
                        File::delete($dbBackupPath);
                    }

                    return redirect()->route('backups.index')->with('success', 'Base de datos restaurada correctamente.');
                } else {
                    // Rollback
                    if (File::exists($dbBackupPath)) {
                        File::copy($dbBackupPath, $dbPath);
                        File::delete($dbBackupPath);
                    }
                    File::deleteDirectory($tempExtractPath);
                    return redirect()->back()->with('error', 'El archivo de respaldo no contiene database.sqlite válido.');
                }
            } else {
                return redirect()->back()->with('error', 'No se pudo abrir el archivo ZIP de respaldo.');
            }
        } catch (\Exception $e) {
            // Restore from backup on error
            if (isset($dbBackupPath) && File::exists($dbBackupPath)) {
                File::copy($dbBackupPath, $dbPath);
                File::delete($dbBackupPath);
            }
            if (isset($tempExtractPath) && File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }

            return redirect()->back()->with('error', 'Error durante la restauración: ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        $filePath = $this->backupDir . '/' . $filename;

        if (File::exists($filePath) && strpos($filename, '..') === false) {
            File::delete($filePath);
            return redirect()->route('backups.index')->with('success', 'Respaldo eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'Archivo no encontrado.');
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'activo' => 'nullable|boolean',
            'frecuencia' => 'required|string|in:diario,semanal,mensual',
            'hora' => 'required|string',
        ]);

        try {
            $settingsPath = storage_path('app/backup_settings.json');
            $settings = [
                'activo' => $request->has('activo') ? (bool) $request->activo : false,
                'frecuencia' => $request->frecuencia,
                'hora' => $request->hora,
            ];

            File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

            return redirect()->route('backups.index')->with('success', 'Configuración de respaldos guardada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
