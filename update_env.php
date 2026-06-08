<?php
$env = file_get_contents('.env');
$pos = strpos($env, 'VITE_APP_NAME');
if ($pos !== false) {
    // cut off the end
    $endOfVite = strpos($env, "\n", $pos);
    if ($endOfVite !== false) {
        $env = substr($env, 0, $endOfVite + 1);
    }
}
$env .= "\nASAAS_API_KEY=\$aact_prod_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjcwNjYyMzc5LTZjOTYtNDAyNi04MmEzLTdiNmU3OTNhMWY0ZDo6JGFhY2hfYTZhZjRkM2UtMjBkNi00NmJhLWE4M2ItZjEyMGRjZjQyODlk\n";
$env .= "ASAAS_API_URL=https://api.asaas.com/v3\n";
file_put_contents('.env', $env);
echo "Done.\n";
