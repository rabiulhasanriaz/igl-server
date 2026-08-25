@extends('admin.master')

@section('sender_id_menu_class','open')
@section('user_sender_id_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.index') }}">Sender ID</a>
        </li>
        <li>
            <a href="{{ route('admin.senderID.userSenderID.index') }}">User Sender ID</a>
        </li>
        <li class="active">Edit</li>
    </ul><!-- /.breadcrumb -->
@endsection

@section('page_header')
    <h1>
        User Sender ID
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Edit
        </small>
    </h1>
@endsection

@section('main_content')

    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-container">

                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $error)
                            <li class="text-danger">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(session()->has('message'))
                    <span class="text-{{ session()->get('type') }}">{{ session()->get('message') }}</span>
                @endif

                <form action="{{ route('admin.senderID.userSenderID.update', [$preIds->id]) }}" method="post" class="form-horizontal" role="form">
                    @csrf
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 col-lg-offset-2 col-md-offset-2">
                        <!-- PAGE CONTENT BEGINS -->

                        <!-- Sender ID Type Filter -->
                        <div class="form-group">
                            <label for="sender_type">Select Sender ID Type</label>
                            <select id="sender_type" class="form-control">
                                <option value="">-- Select Type --</option>
                                <option value="masking">Masking</option>
                                <option value="nonmasking">Non-Masking</option>
                                <option value="iptsp">IPTSP</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> SenderID </label>
                            <br />
                            <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="SenderId chose.." name="sender_id" required="">
                                <option value=""></option>
                                @foreach($senders as $sender)
                                    @php
                                        $type = 'unknown';
                                        $operatorName = 'N/A';
                                        $senderId = $sender->sir_sender_id;
                                        $assignedCompanies = [];
                                        $isAssigned = false;
                                        $assignedDisplay = '';
                                        
                                        // Check all companies this sender ID is assigned to
                                        if(isset($userSenders)) {
                                            foreach($userSenders as $userSender) {
                                                if ($userSender->sender_id == $sender->id) {
                                                    $isAssigned = true;
                                                    $companyName = isset($userSender->user->company_name) ? $userSender->user->company_name : 'Unknown Company';
                                                    $assignedCompanies[] = $companyName;
                                                }
                                            }
                                        }
                                        
                                        // Prepare display for assigned companies
                                        if ($isAssigned) {
                                            $totalCompanies = count($assignedCompanies);
                                            if ($totalCompanies <= 5) {
                                                $assignedDisplay = implode(', ', $assignedCompanies);
                                            } else {
                                                $firstFive = array_slice($assignedCompanies, 0, 5);
                                                $assignedDisplay = implode(', ', $firstFive) . ', ...';
                                            }
                                        }
                                        
                                        // Determine type and operator
                                        if (preg_match('/^[A-Za-z]/', $senderId)) {
                                            $type = 'masking';
                                        } elseif (preg_match('/^(8801|01)/', $senderId)) {
                                            $type = 'nonmasking';
                                        } elseif (preg_match('/^(8809|09)/', $senderId)) {
                                            $type = 'iptsp';
                                            
                                            // Match sender ID with operator numbers
                                            if(isset($operators)) {
                                                foreach($operators as $operator) {
                                                    if (strpos($senderId, $operator->ope_number) === 0) {
                                                        $operatorName = $operator->ope_operator_name;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $sender->id }}" 
                                            @if($sender->id == $preIds->sender_id) selected @endif
                                            data-type="{{ $type }}"
                                            data-operator="{{ $operatorName }}"
                                            class="{{ $isAssigned ? 'assigned-option' : 'available-option' }}">
                                        {{ $senderId }} 
                                        @if($type == 'iptsp' && $operatorName != 'N/A')
                                            ({{ $operatorName }})
                                        @endif
                                        @if($isAssigned)
                                            - [{{ $assignedDisplay }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Legend -->
                            <div style="margin-top: 10px; padding: 8px 12px; background: #f5f5f5; border-radius: 4px; border: 1px solid #e0e0e0;">
                                <span style="display: inline-block; width: 20px; height: 20px; border-radius: 3px; margin-right: 5px; vertical-align: middle; background: #e8f5e9; border: 1px solid #4caf50;"></span>
                                <span style="vertical-align: middle; margin-right: 15px; font-size: 12px;">Available</span>
                                
                                <span style="display: inline-block; width: 20px; height: 20px; border-radius: 3px; margin-right: 5px; vertical-align: middle; background: #fff3e0; border: 1px solid #ff9800;"></span>
                                <span style="vertical-align: middle; margin-right: 15px; font-size: 12px;">Already Assigned to Company</span>
                                
                                @if($preIds->sender)
                                    @php
                                        $currentSenderId = $preIds->sender->sir_sender_id;
                                        $currentType = 'unknown';
                                        $currentOperator = 'N/A';
                                        if (preg_match('/^[A-Za-z]/', $currentSenderId)) {
                                            $currentType = 'masking';
                                        } elseif (preg_match('/^(8801|01)/', $currentSenderId)) {
                                            $currentType = 'nonmasking';
                                        } elseif (preg_match('/^(8809|09)/', $currentSenderId)) {
                                            $currentType = 'iptsp';
                                            if(isset($operators)) {
                                                foreach($operators as $operator) {
                                                    if (strpos($currentSenderId, $operator->ope_number) === 0) {
                                                        $currentOperator = $operator->ope_operator_name;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <span style="margin-left: 15px; font-size: 12px; color: #666;">
                                        <i class="fa fa-info-circle"></i> 
                                        Current: {{ $currentSenderId }} 
                                        @if($currentType == 'iptsp' && $currentOperator != 'N/A')
                                            ({{ $currentOperator }})
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="form-field-select-3"> Company name </label>
                            <br />
                            <select class="chosen-select form-control" id="form-field-select-3" data-placeholder="Company name.." name="user_id" required="">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if($user->id==$preIds->user_id) selected @endif> {{ $user->company_name }} -
                                        ( {{ $user->cellphone }} )
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="clearfix form-group">
                            <input type="submit" class="btn btn-info" value="Update">
                            &nbsp; &nbsp; &nbsp;
                            <button class="btn btn-danger" type="reset">
                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                Reset
                            </button>
                            &nbsp; &nbsp; &nbsp;
                            <a href="{{ route('admin.senderID.userSenderID.index') }}" class="btn btn-default">
                                <i class="ace-icon fa fa-arrow-left"></i>
                                Cancel
                            </a>
                        </div>
                    </div>

                </form>
            </div>

        </div><!-- /.col -->

    </div><!-- /.row -->

@endsection

@section('custom_style')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/chosen.min.css" />
    <style>
        .chosen-select .chosen-results li {
            padding: 8px 12px !important;
            font-size: 13px;
        }
        
        .chosen-select .chosen-results li.assigned-option {
            background-color: #fff3e0 !important;
            color: #bf360c !important;
            border-left: 3px solid #ff9800 !important;
        }
        
        .chosen-select .chosen-results li.available-option {
            background-color: #e8f5e9 !important;
            color: #1b5e20 !important;
        }
        
        .chosen-select .chosen-results li.assigned-option:hover,
        .chosen-select .chosen-results li.assigned-option.highlighted {
            background-color: #ffcc80 !important;
            color: #bf360c !important;
            background-image: none !important;
        }
        
        .chosen-select .chosen-results li.available-option:hover,
        .chosen-select .chosen-results li.available-option.highlighted {
            background-color: #a5d6a7 !important;
            color: #1b5e20 !important;
            background-image: none !important;
        }
        
        .chosen-select .chosen-results li.assigned-option:before {
            content: "⚠ ";
            font-size: 12px;
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        // Initialize Chosen
        $('.chosen-select').chosen({allow_single_deselect:true});
        
        // Filter functionality
        document.getElementById('sender_type').addEventListener('change', function () {
            const selectedType = this.value;
            const senderSelect = document.getElementById('form-field-select-3');
            const options = senderSelect.querySelectorAll('option');

            options.forEach(option => {
                const type = option.getAttribute('data-type');

                // Always show the placeholder option
                if (!option.value) {
                    option.style.display = '';
                    return;
                }

                if (!selectedType || type === selectedType) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Reset the selected option
            senderSelect.value = '';
            // If using Chosen plugin, trigger update
            if ($(senderSelect).hasClass('chosen-select')) {
                $(senderSelect).trigger("chosen:updated");
            }
        });
    </script>
@endsection