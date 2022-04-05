@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'shots'])
    </div>
    <?php
    echo "<h4>Screenshots</h4>";
    $directory = public_path() . "/screenshots"; $images = glob($directory . "/*.png");
    foreach($images as $img){
        $file = explode("/var/www/html/public", $img);
        echo '<div class="col-3 mb-5"><p>' . $file[1] . '</p><a href="' . $file[1] . '" target="_blank"><img src="' . $file[1] . '" class="img-fluid"></a></div>';
    }
    ?>
@endsection
