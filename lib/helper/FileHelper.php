<?php

if (!function_exists('file_format_size')) {

    function file_format_size($data) {
        if ($data < 1024)
            return $data . ' ' . __('bytes');
        else if ($data < 1024000)
            return round(( $data / 1024), 1) . ' ' . __('KB');
        else
            return round(( $data / 1024000), 1) . ' ' . __('MB');
    }

}

if (!function_exists('file_perms_to_human')) {
    function file_perms_to_human($perms) {
        if (($perms & 0xC000) == 0xC000) {
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            $info = 'p';
        } else {
            $info = 'u';
        }

        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
                        (($perms & 0x0800) ? 's' : 'x' ) :
                        (($perms & 0x0800) ? 'S' : '-'));

        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
                        (($perms & 0x0400) ? 's' : 'x' ) :
                        (($perms & 0x0400) ? 'S' : '-'));

        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
                        (($perms & 0x0200) ? 't' : 'x' ) :
                        (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }
}

if (!function_exists('file_get_owner')) {
    function file_get_owner($owner, $field) {
        if (function_exists('posix_getpwuid')) {
            $tmp = false;            
            $tmp = @posix_getpwuid($owner);
            return ($tmp) ? $tmp[$field] : $owner;
        } else return $owner;
    }
}

if (!function_exists('file_get_group')) {
    function file_get_group($group, $field) {
        if (function_exists('posix_getgrgid')) {
            $tmp = false;
            $tmp = @posix_getgrgid($group);
            return ($tmp) ? $tmp[$field] : $group;
        } else return $group;
    }
}