<?php
$args = [];
foreach (['start', 'num', 'hl', 'callback', 'q', 'rsz', 'apikey', 'cx', 'context'] as $key) {
  if (array_key_exists($key, $_GET)) {
    $args[':'.$key.':'] = urlencode($_GET[$key]);
  }
}
$searchForm = file_get_contents(strtr('https://cse.google.com/cse.js?hpg=1&cx=:cx:', $args));
if (preg_match('/\n}\)\(({\n.*)\);/s', $searchForm, $m)) {
  $opts = json_decode($m[1]);
  $args += [':language' => $opts->language, ':cse_token' => $opts->cse_token];
  $results = file_get_contents(strtr('https://cse.google.com/cse/element/v1?rsz=filtered_cse&num=:num:&hl=:hl:&source=gcsc&gss=.com&cx=:cx:&q=:q:&safe=off&cse_tok=:cse_token:&sort=&exp=csqr&callback=:callback:', $args));
  if ($results) {
    $results = preg_replace(strtr('@/\*O_o\*/\s*:callback:\((.*)\);@s', $args), '$1', $results);
    header('Content-Type: text/javascript; charset=UTF-8');
    echo strtr(':callback:(:context:, :results:);', [':results:' => $results] + $args);
  }
}
