@extends('user.support.layout')

@section('support_title', 'Create Support Ticket')
@section('support_subtitle', 'Submit a new support request')
@section('support_ticket', 'active')
@section('support_breadcrumb')
    <li class="active">Create Ticket</li>
@endsection

@section('support_content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="widget-title">Create New Support Ticket</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main">
                        <form method="POST" action="{{ route('user.support.tickets.store') }}" id="create-ticket-form">
                            @csrf
                            
                            <div class="form-group">
                                <label for="subject">Subject *</label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="{{ old('subject') }}" required placeholder="Brief description of your issue">
                                @if($errors->has('subject'))
                                    <span class="text-danger">{{ $errors->first('subject') }}</span>
                                @endif
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category *</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Technical Issue" {{ old('category') == 'Technical Issue' ? 'selected' : '' }}>
                                                Technical Issue
                                            </option>
                                            <option value="Billing" {{ old('category') == 'Billing' ? 'selected' : '' }}>
                                                Billing
                                            </option>
                                            <option value="Account" {{ old('category') == 'Account' ? 'selected' : '' }}>
                                                Account
                                            </option>
                                            <option value="Service" {{ old('category') == 'Service' ? 'selected' : '' }}>
                                                Service
                                            </option>
                                            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>
                                                Other
                                            </option>
                                        </select>
                                        @if($errors->has('category'))
                                            <span class="text-danger">{{ $errors->first('category') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority">Priority *</label>
                                        <select class="form-control" id="priority" name="priority" required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                                Low
                                            </option>
                                            <option value="medium" {{ old('priority') == 'medium' || !old('priority') ? 'selected' : '' }}>
                                                Medium
                                            </option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                                High
                                            </option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                                Urgent
                                            </option>
                                        </select>
                                        @if($errors->has('priority'))
                                            <span class="text-danger">{{ $errors->first('priority') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea class="form-control" id="description" name="description" rows="8" 
                                          required placeholder="Please provide detailed information about your issue">{{ old('description') }}</textarea>
                                @if($errors->has('description'))
                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                @endif
                                <small class="help-block">Provide as much detail as possible to help us resolve your issue quickly.</small>
                            </div>
                            
                            <div class="form-actions center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ace-icon fa fa-check"></i>
                                    Submit Ticket
                                </button>
                                &nbsp; &nbsp; &nbsp;
                                <a href="{{ route('user.support.tickets') }}" class="btn btn-default">
                                    <i class="ace-icon fa fa-times"></i>
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Optional: Add jQuery validation
        $('#create-ticket-form').on('submit', function(e) {
            let isValid = true;
            $('.text-danger').remove();
            
            // Check subject
            if ($('#subject').val().trim().length < 5) {
                $('#subject').after('<span class="text-danger">Subject must be at least 5 characters</span>');
                isValid = false;
            }
            
            // Check category
            if ($('#category').val() === '') {
                $('#category').after('<span class="text-danger">Please select a category</span>');
                isValid = false;
            }
            
            // Check priority
            if ($('#priority').val() === '') {
                $('#priority').after('<span class="text-danger">Please select a priority</span>');
                isValid = false;
            }
            
            // Check description
            if ($('#description').val().trim().length < 10) {
                $('#description').after('<span class="text-danger">Description must be at least 10 characters</span>');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
