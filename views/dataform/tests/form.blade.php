@extends('dataform.tests.master')

@section('content')


<h1>DataForm</h1>
<p>

{{ $form }}
</p>
    {{ document_code(app('path').'/index.php', 15,16) }}
    {{ document_code(app('path').'/views/dataform/tests/form.blade.php') }}
@stop