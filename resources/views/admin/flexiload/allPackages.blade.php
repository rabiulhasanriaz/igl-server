@extends('admin.master')

@section('flexiload_menu_class','open')
@section('flexiload_packages_menu_class', 'active')
@section('page_location')
    <ul class="breadcrumb">
        <li>
            <i class="ace-icon fa fa-home home-icon"></i>
            <a href="{{ route('admin.index') }}">Dashboard</a>
        </li>
        <li>
            <a href="{{ route('admin.index') }}">Flexiload</a>
        </li>
        <li class="active">All Packages</li>
    </ul><!-- /.breadcrumb -->
@endsection


@section('page_header')
    <h1>
        Flexiload
        <small>
            <i class="ace-icon fa fa-angle-double-right"></i>
            Packages
            <button class="btn btn-primary btn-sm pull-right" id="addPackagebtn">Add a Package</button>
        </small>
    </h1>
@endsection

@section('main_content')
    @if($errors->any())
        @foreach( $errors->all() as $error )
            <li>{{ $error }}</li>
        @endforeach
    @endif
    
    <!-- Button trigger modal -->
    
    <!-- Add package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <p class="h4 modal-title text-primary" id="exampleModalLabel">Add A Package</p>
          </div>
          <form action="{{ route('admin.flexiload.addPackage') }}" method="post"> 
          @csrf
              <div class="modal-body">
                    <div class="form-group">
                        <label for="package_category">Package Category</label>
                        <select id="package_category" name="package_category" class="form-control input-sm" required>
                            <option>Select a category</option>
                            <option value="1">Minute</option>
                            <option value="2">SMS</option>
                            <option value="3">Data</option>
                            <option value="4">Combo</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="operator">Opearator</label>
                        <select class="form-control input-sm" name="operator" required> 
                            @foreach($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->ope_operator_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="package_price" class="form-control input-sm" placeholder="package price" required>
                    </div>

                    <div class="form-group">
                        <label for="package_details">Package Details</label>
                        <input type="text" name="package_details" class="form-control input-sm" placeholder="Package Details" required>
                    </div>

                    <div class="form-group">
                        <label for="commission">Commission</label>
                        <input type="number" name="commission" class="form-control input-sm" placeholder="0.0" step="0.1">
                    </div>

                    <div class="form-group">
                        <label for="validity">Package Validity</label>
                        <input type="text" name="validity" class="form-control input-sm" placeholder="Package validity" required>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add This Package</button>
              </div>
          </form>
        </div>
      </div>
    </div>


    <div class="space-6"></div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @include('admin.partials.session_messages')
            <table id="flexiload-eligible-user-list-table" class="table table-striped table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Category</th>
                        <th>Operator</th>
                        <th>Price</th>
                        <th>Details</th>
                        <th>Validity</th>
                        <th>Comission</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @php($serial=1)
                    @foreach( $packages as $package )
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>
                            @if($package->package_category == 1)
                                <span class="text-primary">{{ 'Minute' }}</span>
                            @elseif($package->package_category == 2)
                                <span class="text-primary">{{ 'SMS' }}</span>
                            @elseif($package->package_category == 3)
                                <span class="text-primary">{{ 'Data' }}</span>
                            @elseif($package->package_category == 4)
                                <span class="text-primary">{{ 'Combo' }}</span>
                            @else
                                <span class="text-primary">{{ 'N / A' }}</span>
                            @endif
                        </td>
                        <td>{{ $package->operator['ope_operator_name'] }}</td>
                        <td>{{ $package->package_price }}</td>
                        <td>{{ $package->package_details ?? 'N/A' }}</td>
                        <td>{{ $package->validity }}</td>

                        <td>{{ $package->commission  }} Tk</td>
                        <td class="{{ $package->status==1?'text-success':'text-danger'}}">{{ $package->status==1?'Active':'Inactive'  }}</td>

                        <td>
                            <div class="widget-toolbar no-border">
                                <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false">
                                    Action
                                    <i class="ace-icon fa fa-chevron-down icon-on-right"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-primary dropdown-menu-right dropdown-caret dropdown-close">
                                    <li>
                                        <a data-toggle="modal" class="edit-modal-link" 
                                        onclick="editPackageModal(
                                            '{{ $package->id }}', 
                                            '{{ $package->package_category }}', 
                                            '{{ $package->operator_id }}', 
                                            '{{ $package->package_price }}', 
                                            '{{ $package->package_details }}', 
                                            '{{ $package->commission }}', 
                                            '{{ $package->validity }}'
                                            )">
                                        <i class="ace-icon fa fa-search-plus bigger-130"></i>
                                        Edit
                                        </a>
                                    </li>
                                    
                                    <li class="divider"></li>

                                    <li>
                                        <a href="{{ route('admin.flexiload.activeInactive', ['id'=>$package->id]) }}" >
                                        <i class="ace-icon fa fa-search-plus bigger-130"></i>
                                            {{ $package->status==1?'InActive':'Active'  }}
                                        </a>
                                    </li>
                                    
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Editing a package Modal -->
            <div class="modal fade" id="editPackageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="exampleModalLabel">Edit Package</h5>
                  </div>
                  <form action="{{ route('admin.flexiload.editPackage') }}" method="post"> 
                  @csrf
                  <div class="modal-body">
                    <input type="number" name="package_id" hidden>
                    <div class="form-group">
                        <label for="e_package_category">Package Category</label>
                        <select id="e_package_category" name="e_package_category" class="form-control input-sm" required>
                            <option>Select a category</option>
                            <option value="1">Minute</option>
                            <option value="2">SMS</option>
                            <option value="3">Data</option>
                            <option value="4">Combo</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="e_operator">Opearator</label>
                        <select class="form-control input-sm" name="e_operator" required> 
                            @foreach($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->ope_operator_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="e_price">Price</label>
                        <input type="number" id="e_price" name="e_package_price" class="form-control input-sm" placeholder="package price" required>
                    </div>

                    <div class="form-group">
                        <label for="e_package_details">Package Details</label>
                        <input type="text" id="e_package_details" name="e_package_details" class="form-control input-sm" placeholder="Package Details" required>
                    </div>

                    <div class="form-group">
                        <label for="e_commission">Commission</label>
                        <input type="number" id="e_commission" name="e_commission" class="form-control input-sm" placeholder="0.0" step="0.1">
                    </div>

                    <div class="form-group">
                        <label for="e_validity">Package Validity</label>
                        <input type="text" id="e_validity" name="e_validity" class="form-control input-sm" placeholder="Package validity" required>
                    </div>
                  </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                      </div>
                  </form>
                </div>
              </div>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection


@section('custom_style')
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
          -webkit-appearance: none; 
          margin: 0; 
        }
    </style>
@endsection

@section('custom_script')
    <script src="{{ asset('assets') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets') }}/js/jquery.dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">
        $('#flexiload-eligible-user-list-table').DataTable();
        $('#flexiload-non-eligible-user-list-table').DataTable();

        function editPackageModal(id, package_category, operator_id, package_price, package_details, commission, validity)
        {
            $("input[name='package_id']").val(id);
            $("select[name='e_package_category']").val(package_category);
            $("select[name='e_operator']").val(operator_id);
            $("input[name='e_package_price']").val(package_price);
            $("input[name='e_package_details']").val(package_details);
            $("input[name='e_package_details']").text(package_details);
            $("input[name='e_commission']").val(commission);
            $("input[name='e_validity']").val(validity);

            $("#editPackageModal").modal();
        } 
        $(document).ready(function(){
            $("#addPackagebtn").click(function(){
              $("#addPackageModal").modal();  
            })
        })
    </script>

@endsection
