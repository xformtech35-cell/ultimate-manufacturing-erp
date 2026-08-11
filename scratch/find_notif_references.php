<?php
// Simple script to find any insert or select references to 'notifications' table in php files
function search_dir($dir) {
    $it = new RecursiveDirectoryIterator($dir);
    foreach (new RecursiveIteratorIterator($it) as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (stripos($content, 'notifications') !== false) {
                if (stripos($content, '->db->') !== false || stripos($content, 'dbprefix') !== false) {
                    echo "Found reference in: " . $file->getPathname() . "\n";
                }
            }
        }
    }
}
search_dir('application/controllers');
search_dir('application/models');
