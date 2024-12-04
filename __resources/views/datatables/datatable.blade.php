@section('css')
    @include('datatables.css')
@endsection

{!! $dataTable->table(['width' => '100%', 'class' => 'table table-striped table-bordered']) !!}

@push('scripts')
    @include('datatables.js')
    {!! $dataTable->scripts() !!}
@endpush
