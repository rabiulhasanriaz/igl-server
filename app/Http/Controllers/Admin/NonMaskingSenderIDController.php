<?php

namespace App\Http\Controllers\Admin;

use App\Model\SenderIdNonMasking;
use App\Model\SenderIdRegister;
use App\Model\Operator;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class NonMaskingSenderIDController extends Controller
{
    /**
     * Show non masking sender ID list
     */
    public function index()
    {
        $nonMaskingSenderIds = SenderIdNonMasking::with('operator')->get();
        $operators = Operator::all();
        return view('admin.senderId.non_masking_sender_id_list', [
            'nonMaskingSenderIds' => $nonMaskingSenderIds,
            'operators' => $operators
        ]);
    }

    /**
     * Add new non masking sender ID
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nonmasking' => ['required', 'unique:sender_id_non_maskings,number'],
            'operator_id' => ['nullable', 'exists:operators,id']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        try {
            $sender = SenderIdNonMasking::create([
                'number' => $request->input('nonmasking'),
                'operator_id' => $request->input('operator_id')
            ]);

            // Create register entry
            SenderIdRegister::create([
                'sir_sender_id' => $sender->number,
            ]);

            return redirect()->back()
                ->with('alert_type', 'success')
                ->with('message', 'Successfully added non masking sender ID');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('alert_type', 'danger')
                ->with('message', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show form of edit non masking sender ID
     */
    public function edit($id)
    {
        $nonMaskingSenderId = SenderIdNonMasking::find($id);
        
        if (!$nonMaskingSenderId) {
            return redirect()->route('admin.senderID.nonMaskingSenderID.index')
                ->with('alert_type', 'danger')
                ->with('message', 'Non-masking sender ID not found');
        }

        $operators = Operator::all();
        
        return view('admin.senderId.edit_non_masking_sender_id', [
            'nonMaskingSenderId' => $nonMaskingSenderId,
            'operators' => $operators
        ]);
    }

    /**
     * Update non masking sender ID
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nonmasking' => [
                'required',
                Rule::unique('sender_id_non_maskings', 'number')->ignore($id)
            ],
            'operator_id' => ['nullable', 'exists:operators,id']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validator);
        }

        $sender = SenderIdNonMasking::find($id);
        if (!$sender) {
            return redirect()->route('admin.senderID.nonMaskingSenderID.index')
                ->with('alert_type', 'danger')
                ->with('message', 'Non-masking sender ID not found');
        }

        try {
            $oldNumber = $sender->number;
            $sender->number = $request->input('nonmasking');
            $sender->operator_id = $request->input('operator_id');
            $sender->save();

            // Update related register entry
            $register = SenderIdRegister::where('sir_sender_id', $oldNumber)->first();
            if ($register) {
                $register->sir_sender_id = $sender->number;
                $register->save();
            }

            return redirect()->route('admin.senderID.nonMaskingSenderID.index')
                ->with('alert_type', 'success')
                ->with('message', 'Successfully updated non masking sender ID');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('alert_type', 'danger')
                ->with('message', 'Something went wrong while updating: ' . $e->getMessage());
        }
    }

    /**
     * Delete non masking sender ID
     */
    public function delete($id)
    {
        $sender = SenderIdNonMasking::find($id);
        
        if (!$sender) {
            return redirect()->back()
                ->with('alert_type', 'danger')
                ->with('message', 'Non-masking sender ID not found');
        }

        try {
            // Delete related register entries
            SenderIdRegister::where('sir_sender_id', $sender->number)->delete();
            
            // Delete sender
            $sender->delete();

            return redirect()->back()
                ->with('alert_type', 'success')
                ->with('message', 'Non-masking sender ID deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('alert_type', 'danger')
                ->with('message', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Import CSV or TXT files for sender IDs
     */
    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
            'operator_id' => 'required|exists:operators,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('file');
        
        try {
            // Move the uploaded file to a readable location
            $destinationPath = storage_path('app/uploads/temp/');
            
            // Create directory if it doesn't exist
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            
            // Generate unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $destinationPath . $filename;
            
            // Move the file to our storage directory
            $file->move($destinationPath, $filename);
            
            // Check if file exists and is readable
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('Could not read the uploaded file.');
            }
            
            $extension = strtolower($file->getClientOriginalExtension());
            $operatorId = $request->input('operator_id');
            $senderIds = [];
            $inserted = 0;
            $duplicate = 0;
            $invalid = 0;
            
            // Process based on file type
            if ($extension === 'csv' || $extension === 'txt') {
                $senderIds = $this->processCsvFile($filePath);
            } 
            elseif ($extension === 'xlsx' || $extension === 'xls') {
                // Clean up temp file
                File::delete($filePath);
                return redirect()->back()
                    ->with('alert_type', 'warning')
                    ->with('message', 'Excel files are not supported. Please save your file as CSV format and try again.')
                    ->withInput();
            }
            
            // Clean up temp file
            File::delete($filePath);
            
            // Remove duplicates and empty values
            $senderIds = array_unique(array_filter($senderIds));
            
            if (empty($senderIds)) {
                return redirect()->back()
                    ->with('alert_type', 'warning')
                    ->with('message', 'No valid sender IDs found in the file.')
                    ->withInput();
            }
            
            foreach ($senderIds as $senderId) {
                // Validate sender ID
                if (!preg_match('/^\d{3,15}$/', $senderId)) {
                    $invalid++;
                    continue;
                }
                
                // Check if sender ID already exists
                if (!SenderIdNonMasking::where('number', $senderId)->exists()) {
                    $sender = SenderIdNonMasking::create([
                        'number' => $senderId,
                        'operator_id' => $operatorId
                    ]);

                    SenderIdRegister::create([
                        'sir_sender_id' => $sender->number,
                    ]);

                    $inserted++;
                } else {
                    $duplicate++;
                }
            }

            $message = "$inserted sender IDs imported successfully!";
            if ($duplicate > 0) {
                $message .= " $duplicate duplicate(s) skipped.";
            }
            if ($invalid > 0) {
                $message .= " $invalid invalid sender ID(s) skipped (must be 3-15 digits).";
            }
            
            if ($inserted == 0 && $duplicate == 0 && $invalid > 0) {
                return redirect()->back()
                    ->with('alert_type', 'danger')
                    ->with('message', $message)
                    ->withInput();
            }

            return redirect()->back()
                ->with('alert_type', 'success')
                ->with('message', $message);
                
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()
                ->with('alert_type', 'danger')
                ->with('message', 'Import failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Process CSV/TXT file and extract sender IDs
     */
    protected function processCsvFile($filePath)
    {
        $senderIds = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read first line to detect delimiter
            $firstLine = fgets($handle);
            rewind($handle);
            
            $delimiter = ',';
            if (strpos($firstLine, "\t") !== false) {
                $delimiter = "\t";
            } elseif (strpos($firstLine, ';') !== false) {
                $delimiter = ';';
            } elseif (strpos($firstLine, '|') !== false) {
                $delimiter = '|';
            } elseif (strpos($firstLine, ' ') !== false && strpos($firstLine, ',') === false) {
                $delimiter = ' ';
            }
            
            // Read file line by line
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                foreach ($data as $value) {
                    $value = trim($value);
                    $value = str_replace('"', '', $value);
                    $value = str_replace("'", '', $value);
                    
                    if (!empty($value)) {
                        // Extract numeric values
                        if (preg_match('/\d+/', $value, $matches)) {
                            $senderIds[] = $matches[0];
                        }
                    }
                }
            }
            fclose($handle);
        } else {
            throw new \Exception('Could not open the file for reading.');
        }
        
        return $senderIds;
    }

    /**
     * Extract sender ID from a row (first column only)
     */
    protected function extractSenderIdFromRow($row, &$senderIds)
    {
        if (count($row) < 1) {
            return;
        }

        $senderIdRaw = trim($row[0] ?? '');
        
        if (empty($senderIdRaw)) {
            return;
        }

        if (preg_match('/\d+/', $senderIdRaw, $matches)) {
            $senderId = $matches[0];
            $senderIds[] = $senderId;
        }
    }

    /**
     * Process a single row from import file
     */
    protected function processImportRow($row, &$allData)
    {
        if (count($row) < 1) {
            return;
        }

        $senderIdRaw = trim($row[0] ?? '');
        $operatorValueRaw = trim($row[1] ?? '');

        if (!ctype_digit($senderIdRaw)) {
            return;
        }

        $senderId = $senderIdRaw;
        $operatorId = null;

        if (ctype_digit($operatorValueRaw) && Operator::where('id', (int)$operatorValueRaw)->exists()) {
            $operatorId = (int)$operatorValueRaw;
        }

        $allData[] = [
            'number' => $senderId,
            'operator_id' => $operatorId
        ];
    }
}
