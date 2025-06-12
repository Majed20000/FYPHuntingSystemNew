<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Coordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;


class UserRegisterController extends Controller
{
    //Function retrieve the csv file and store the data in the database
    public function store(Request $request)
    {
        //Validate the csv file and of the correct format
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        //To retrieve the csv file from the request
        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        //To retrieve the records from the csv file and store it in the $records variable
        $records = $csv->getRecords();
        $successCount = 0;
        $failureCount = 0;
        $errors = [];


        DB::beginTransaction();

        try {
            //To iterate through each record in the csv file
            foreach ($records as $rowNumber => $record) {
                try {
                    //To generate a temporary password for the user
                    $tempPassword = Str::random(10);
                    // Create user
                    $user = User::create([
                        'name' => $record['name'],
                        'email' => $record['email'],
                        'matric_number' => $record['matric_number'],
                        'password' => Hash::make($tempPassword),  // Hash the temporary password
                        'role' => strtolower($record['role']),
                        'first_login' => true
                    ]);

                    // Create role-specific record
                    if ($user->role === 'student') {
                        Student::create([
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'matric_id' => $user->matric_number,
                            'email' => $user->email
                        ]);
                    } elseif ($user->role === 'lecturer') {
                        try {
                            // \Log::info('Creating lecturer with data:', [
                            //     'user_id' => $user->id,
                            //     'name' => $user->name,
                            //     'staff_id' => $user->matric_number,
                            //     'email' => $user->email
                            // ]);

                            Lecturer::create([
                                'user_id' => $user->id,
                                'name' => $user->name,
                                'staff_id' => $user->matric_number,
                                'email' => $user->email,
                                'phone' => "",
                                'research_group' => "",
                                'max_students' => 5,
                                'current_students' => 0,
                                'accepting_students' => true
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error creating lecturer record: ' . $e->getMessage());
                            //\Log::error('Stack trace: ' . $e->getTraceAsString());
                            throw $e;
                        }
                    }

                    // Send email with temporary password
                    Mail::to($user->email)->send(new \App\Mail\UserCredentials([ //Send data to the email in the UserCredentials.php file
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $tempPassword,
                        'role' => $user->role
                    ]));

                    $successCount++;
                } catch (\Exception $e) {
                    \Log::error('Error in row ' . ($rowNumber + 2) . ': ' . $e->getMessage());
                    $failureCount++;
                    $errors[] = "Error in row " . ($rowNumber + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            return back()->with([
                'success' => "{$successCount} users were created successfully.",
                'failures' => $failureCount > 0 ? "{$failureCount} users failed to be created." : null,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the file: ' . $e->getMessage());
        }
    }


    //To retrieve the users from the database and display them in the userRegister view
    public function index(Request $request)
    {
        //To create a query to retrieve the users from the database
        $query = User::query();

        // search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%") //To search for the name
                    ->orWhere('email', 'like', "%{$search}%") //To search for the email
                    ->orWhere('matric_number', 'like', "%{$search}%"); //To search for the matric number
            });
        }

        // Apply role filter
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('coordinator.userRegister', compact('users'));
    }



    //To delete a user from the database
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Don't allow deletion of the last coordinator
            if ($user->role === 'coordinator') {
                $coordinatorCount = User::where('role', 'coordinator')->count();
                if ($coordinatorCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete the last coordinator'
                    ], 400);
                }
            }

            DB::beginTransaction();

            // Delete related records based on role
            if ($user->role === 'student') {
                Student::where('user_id', $user->id)->delete();
            } elseif ($user->role === 'lecturer') {
                Lecturer::where('user_id', $user->id)->delete();
            } elseif ($user->role === 'coordinator') {
                Coordinator::where('user_id', $user->id)->delete();
            }

            // Delete the user
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }
    public function exportExcelWithPhpSpreadsheet()
    {
        $users = \App\Models\User::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->fromArray(['Name', 'Email', 'Role', 'ID Number', 'Created At'], null, 'A1');

        // Fill data
        $row = 2;
        foreach ($users as $user) {
            $sheet->fromArray([
                $user->name,
                $user->email,
                ucfirst($user->role),
                $user->matric_number,
                $user->created_at->format('Y-m-d H:i:s')
            ], null, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'users-list.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
    public function exportPdf()
    {
        $users = User::all();
        $pdf = PDF::loadView('coordinator.exports.users-pdf', compact('users'));
        return $pdf->download('users-list.pdf');
    }
}
