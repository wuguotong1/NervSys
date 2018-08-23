<?php

/**
 * File I/O Extension
 *
 * Copyright 2016-2018 秋水之冰 <27206617@qq.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ext;

use core\handler\factory;

class file extends factory
{
    /**
     * Get file extension (in lowercase)
     *
     * @param string $path
     *
     * @return string
     */
    public static function get_ext(string $path): string
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ('' !== $ext) {
            $ext = strtolower($ext);
        }

        unset($path);
        return $ext;
    }

    /**
     * Check & create directory (Related to "$root")
     *
     * @param string $path
     * @param string $root
     * @param int    $mode
     *
     * @return string
     */
    public static function get_path(string $path, string $root = ROOT, int $mode = 0774): string
    {
        //Parent directory is not allowed
        if (false !== strpos($path, '..')) {
            $path = str_replace('..', '', $path);
        }

        //Get clean path
        $path = strtr($path, ['/' => DIRECTORY_SEPARATOR, '\\' => DIRECTORY_SEPARATOR]);
        $path = trim($path, DIRECTORY_SEPARATOR);

        //Return root path
        if ('' === $path) {
            return is_readable($root) ? DIRECTORY_SEPARATOR : '';
        }

        //Create directories
        $dir = $root . $path;
        if (!is_dir($dir)) {
            //Create directory recursively
            mkdir($dir, $mode, true);
            //Set permissions to path
            chmod($dir, $mode);
        }

        //Check path property
        $path = is_readable($dir) ? $path . DIRECTORY_SEPARATOR : '';

        unset($root, $mode, $dir);
        return $path;
    }

    /**
     * Get a list of files in a directory or recursively
     *
     * @param string $path
     * @param string $pattern
     * @param bool   $recursive
     *
     * @return array
     */
    public static function get_list(string $path, string $pattern = '*', bool $recursive = false): array
    {
        //Check path
        if (false === $pathname = realpath($path)) {
            return [];
        }

        $pathname .= DIRECTORY_SEPARATOR;
        $list     = glob($pathname . $pattern, GLOB_NOSORT | GLOB_BRACE);

        //Return list on non-recursive
        if (!$recursive) {
            unset($path, $pattern, $recursive, $pathname);
            return $list;
        }

        //Get file list recursively
        $dirs = glob($pathname . '*', GLOB_NOSORT | GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $list = array_merge($list, self::get_list($dir, $pattern, true));
        }

        unset($path, $pattern, $recursive, $pathname, $dirs, $dir);
        return $list;
    }
}