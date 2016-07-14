<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script>CKEDITOR.replace({!! json_encode($name) !!}, {!! json_encode($config) !!});</script>