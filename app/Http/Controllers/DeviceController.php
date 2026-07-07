<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\Datatables;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Attendance;
use DB;

class DeviceController extends Controller
{
    // Menampilkan daftar device
    public function index(Request $request)
    {
        $data['lable'] = "Devices";
        $data['log'] = DB::table('devices')->select('id','nama','no_sn','lokasi','online','type')->orderBy('online', 'DESC')->get();
        return view('devices.index',$data);
    }

    public function DeviceLog(Request $request)
    {
        $data['lable'] = "Devices Log";
        $data['log'] = DB::table('device_log')->select('id','data','url')->orderBy('id','DESC')->get();
        
        return view('devices.log',$data);
    }
    
    public function FingerLog(Request $request)
    {
        $data['lable'] = "Finger Log";
        $data['log'] = DB::table('finger_log')->select('id','data','url')->orderBy('id','DESC')->get();
        return view('devices.log',$data);
    }

    public function Attendance() {
       $attendances = DB::table('attendances')
           ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.employee_id')
           ->select(
               'attendances.id',
               'attendances.sn',
               'attendances.table',
               'attendances.stamp',
               'attendances.employee_id',
               'employees.name as employee_name',
               'attendances.timestamp',
               'attendances.status1',
               'attendances.status2',
               'attendances.status3',
               'attendances.status4',
               'attendances.status5'
           )
           ->orderBy('attendances.id','DESC')
           ->paginate(15);

        return view('devices.attendance', compact('attendances'));
    }

    // Menampilkan form edit device
    public function edit($id)
    {
        $device = DB::table('devices')->where('id', $id)->first();
        return view('devices.edit', compact('device'));
    }

    // Mengupdate device ke database
    public function update(Request $request, $id)
    {
        DB::table('devices')->where('id', $id)->update([
            'nama' => $request->input('nama'),
            'no_sn' => $request->input('no_sn'),
            'lokasi' => $request->input('lokasi'),
            'online' => $request->input('online'),
            'type' => $request->input('type'),
        ]);

        return redirect()->route('devices.index')->with('success', 'Device berhasil diupdate!');
    }

    // --- EMPLOYEE CRUD ---
    public function Employees() {
        $employees = DB::table('employees')->orderBy('id', 'DESC')->paginate(15);
        return view('devices.employees', compact('employees'));
    }

    public function StoreEmployee(Request $request) {
        $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        DB::table('employees')->insert([
            'employee_id' => $request->input('employee_id'),
            'nik' => $request->input('nik'),
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'department' => $request->input('department'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function EditEmployee($id) {
        $employee = DB::table('employees')->where('id', $id)->first();
        return view('devices.edit_employee', compact('employee'));
    }

    public function UpdateEmployee(Request $request, $id) {
        $request->validate([
            'employee_id' => 'required',
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        DB::table('employees')->where('id', $id)->update([
            'employee_id' => $request->input('employee_id'),
            'nik' => $request->input('nik'),
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'department' => $request->input('department'),
            'updated_at' => now(),
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil diupdate!');
    }

    public function DeleteEmployee($id) {
        DB::table('employees')->where('id', $id)->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus!');
    }

    public function ImportEmployees(Request $request) {
        $request->validate([
            'csv_file' => 'required|file',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->with('error', 'Gagal membuka file.');
        }

        // Read header
        $header = fgetcsv($handle, 1000, ',');
        
        // Detect delimiter: if only one element in header, check semicolon
        $delimiter = ';';
        if ($header !== false && count($header) > 1) {
            // It has multiple columns with comma
            $delimiter = ',';
        }

        // Re-read from beginning to parse header and data properly
        rewind($handle);
        $headerRow = fgetcsv($handle, 1000, $delimiter);

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            // Check if row has enough columns (at least 3: ID, NIK, Nama)
            if (count($row) < 3) {
                $skipped++;
                continue;
            }

            $rawId = trim($row[0]);
            $nik = trim($row[1]);
            $name = trim($row[2]);
            $gender = isset($row[3]) ? trim($row[3]) : null;
            $department = isset($row[4]) ? trim($row[4]) : null;

            // Skip empty rows or header duplicate
            if (empty($rawId) || empty($name) || strtolower($rawId) === 'no id.' || strtolower($rawId) === 'no id') {
                $skipped++;
                continue;
            }

            // Normalize ID: remove leading zeros if numeric
            $employeeId = is_numeric($rawId) ? (int)$rawId : $rawId;

            // Insert or Update employee mapping
            DB::table('employees')->updateOrInsert(
                ['employee_id' => $employeeId],
                [
                    'nik' => $nik ?: null,
                    'name' => $name,
                    'gender' => $gender ?: null,
                    'department' => $department ?: null,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())')
                ]
            );

            $imported++;
        }

        fclose($handle);

        return redirect()->route('employees.index')->with('success', "Berhasil mengimpor {$imported} karyawan. (Dilewati: {$skipped})");
    }

    // --- ACCESS SESSIONS LIST ---
    public function AccessSessions() {
        $sessions = DB::table('access_sessions')
            ->leftJoin('employees', 'access_sessions.employee_id', '=', 'employees.employee_id')
            ->select(
                'access_sessions.*',
                'employees.name as employee_name'
            )
            ->orderBy('access_sessions.id', 'DESC')
            ->paginate(15);

        return view('devices.access_sessions', compact('sessions'));
    }
}
