<?php
$source = 'c:\\xampp2\\htdocs\\rideapp\\app\\Modules\\Dispatch\\Views\\trips\\index.php';
$dest = 'c:\\xampp2\\htdocs\\rideapp\\app\\Modules\\Dispatch\\Views\\trips\\_quick_dispatch_modal.php';

$lines = file($source);

$html_start = 309; // Index 309 is line 310 ("<!-- Quick Dispatch Modal -->")
$html_end = 448; // Index 448 is line 449 ("</div>")

$js_start = 634; // Index 634 is line 635 ("<script>")
$js_end = 857; // Index 857 is line 858 ("    });")

$modal_content = "";
for ($i = $html_start; $i <= $html_end; $i++) {
    $modal_content .= $lines[$i];
}

$modal_content .= "\n";

for ($i = $js_start; $i <= $js_end; $i++) {
    $modal_content .= $lines[$i];
}
$modal_content .= "</script>\n";

file_put_contents($dest, $modal_content);

$new_source = [];
for ($i = 0; $i < count($lines); $i++) {
    if ($i >= $html_start && $i <= $html_end) {
        if ($i == $html_start) {
            $new_source[] = "<?= view('Modules\\Dispatch\\Views\\trips\\_quick_dispatch_modal') ?>\n";
        }
        continue;
    }
    if ($i >= $js_start && $i <= $js_end) {
        continue;
    }
    $new_source[] = $lines[$i];
}

file_put_contents($source, implode("", $new_source));
echo "Refactoring complete.\n";
