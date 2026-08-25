@if(session()->has('message'))
    <div class="alert alert-{{ session()->get('type') }}" id="report-alert">
        <button type="button" class="close" data-dismiss="alert"><span style="font-size: 20px;">x</span></button>
        {{ session()->get('message') }}
    </div>
@endif