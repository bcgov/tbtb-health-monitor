@extends('layouts.main')

@section('content')
    <div class="row mb-3">
        @include('nav', ['page' => 'logs'])
    </div>
    <?php
    echo "<h4>Logs</h4>";
    echo '<div class="accordion" id="accordionExample">';
    $directory = public_path() . '/../storage/logs';
    $files = glob($directory . "/monitor*.log");
    $files = array_reverse($files);
    for($i=0; $i<sizeof($files); $i++){
        $file = $files[$i];
        $name = explode("/", $file);
        ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?php echo $i; ?>">
            <button class="accordion-button <?php if($i > 0) echo 'collapsed';?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="<?php if($i == 0) echo 'true'; else echo 'false'; ?>" aria-controls="collapse<?php echo $i; ?>"><?php echo last($name);?></button>
        </h2>
        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse <?php if($i == 0) echo 'show';?>" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#accordionExample">
            <div class="accordion-body bg-white">
                <?php
                if ($f = fopen($file, "r")) {
                    while(!feof($f)) {
                        echo fgets($f) . "<br/>";
                    }
                    fclose($f);
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
@endsection
