<?php

namespace App\Jobs;

use App\Model\PhonebookContact;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class insertPhoneNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $allContacts;
    private $allName;
    private $request;
    private $auth_user;

    public function __construct($allContacts, $allName, $request, $auth_user)
    {
        $this->allContacts = $allContacts;
        $this->allName = $allName;
        $this->request = $request;
        $this->auth_user = $auth_user;
    }


    public function handle()
    {
        $total_number = 0;
        $dataForInsert = array();
        $insertCount = 0;
        $insertedNumber = array();
        try {
            /**/
            $preContacts = PhonebookContact::where(['user_id'=>$this->auth_user, 'category_id'=>$this->request['group_id']])->pluck('phone_number')->toArray();;


            /**/
            foreach ($this->allContacts as $contact) {
                if (isset($this->allName[$total_number])) {
                    $contact_name = $this->allName[$total_number];
                } else {
                    $contact_name = '';
                }
                $total_number++;
                $number = \PhoneNumber::addNumberPrefix($contact);
                if (\PhoneNumber::isValid($number)) {
//                    $checkExist = PhonebookContact::where(['user_id' => $this->auth_user, 'category_id' => $this->request['group_id'], 'phone_number' => $number])->first();
                    if(in_array($number, $insertedNumber)){
                        continue;
                    }
                    else if (!in_array($number, $preContacts)) {
                        $insertedNumber[] = $number;
                        $dataForInsert[] = array(
                            'user_id' => $this->auth_user,
                            'category_id' => $this->request['group_id'],
                            'name' => $contact_name,
                            'designation' => null,
                            'phone_number' => $number,
                            'status' => '1',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        );
                        $insertCount++;
                        if ($insertCount < 50) {
                            $insertCount++;
                        } else {
                            PhonebookContact::insert($dataForInsert);
                            $dataForInsert = array();
                            $insertCount = 0;
                        }

                    }
                }
            }
            PhonebookContact::insert($dataForInsert);
            // Log::info('Phone Number added successfully');
        } catch (\Exception $e) {
            // Log::info('something wrong to insert phone number'.$e->getMessage());
        }
    }
}
