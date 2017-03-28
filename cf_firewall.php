<?php

$cf_list_urls = [
    'http://www.cloudflare.com/ips-v4',
    'http://www.cloudflare.com/ips-v6'
];
$allowed_ports = [80, 443];
$destination_address = '203.0.113.123';
$protocol = 'tcp';
$exec = (isset($argv[1]) && $argv[1] == '1');

foreach ($cf_list_urls as $i => $cf_list_url) {
    if (($ip_list = file_get_contents($cf_list_url)) !== false) {
        $ip_addresses = explode("\n", trim($ip_list));
        foreach ($ip_addresses as $j => $source_address) {
            if (empty($source_address)) {
                continue;
            }
            $last = ($i + 1 == count($cf_list_urls) && $j + 1 == count($ip_addresses));
            $command = sprintf("ufw allow from %s to %s port %s proto %s",
                $source_address, $destination_address, implode(',', $allowed_ports), $protocol);
            if ($exec) {
                exec($command);
            } else {
                fwrite(STDOUT, $command . ($last ? '' : ' &&') . PHP_EOL);
            }
        }
    } else {
        fwrite(STDERR, 'Could not fetch IP addresses list!' . PHP_EOL);
    }
}
