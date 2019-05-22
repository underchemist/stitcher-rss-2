<?php declare(strict_types=1);

foreach ($metrics as $metric) {
    $labels
        = ($metric['labels'] ?? [])
        + $host_labels;

    foreach ($labels as $key => $value) {
        $labels[$key] = sprintf('%s="%s"', $key, $value);
    }

    $labels = implode(",", $labels);

    echo "{$metric['name']}{{$labels}} {$metric['value']}\n";
}
