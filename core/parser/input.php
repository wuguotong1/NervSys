<?php

/**
 * Input Parser
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

namespace core\parser;

use core\system;

class input extends system
{
    //Read options
    const RET  = ['ret', 'r'];
    const CMD  = ['cmd', 'c'];
    const OUT  = ['out', 'o'];
    const DATA = ['data', 'd'];
    const PIPE = ['pipe', 'p'];
    const TIME = ['time', 't'];

    /**
     * Read input
     */
    public static function read(): void
    {
        //Read data
        if (!parent::$is_cli) {
            //Read HTTP & input
            self::read_http();
            self::read_raw();
        } else {
            //Read option & argument
            self::read_argv(self::read_opt());
        }

        //Read command
        if ('' === parent::$cmd
            && !empty($val = self::opt_val(parent::$data, self::CMD))
            && is_string($val['data'])
        ) {
            parent::$cmd = &$val['data'];
        }

        //Read output format
        if ('' === parent::$out
            && !empty($val = self::opt_val(parent::$data, self::OUT))
            && is_string($val['data'])
        ) {
            parent::$out = &$val['data'];
        }

        unset($val);
    }

    /**
     * Read HTTP data
     */
    private static function read_http(): void
    {
        //Read FILES
        if (!empty($_FILES)) {
            parent::$data += $_FILES;
        }

        //Read POST
        if (!empty($_POST)) {
            parent::$data += $_POST;
        }

        //Read GET
        if (!empty($_GET)) {
            parent::$data += $_GET;
        }
    }

    /**
     * Read raw data
     */
    private static function read_raw(): void
    {
        //Read data
        if (empty($input = file_get_contents('php://input'))) {
            return;
        }

        //Decode data in JSON/XML
        if (is_array($data = json_decode($input, true))) {
            parent::$data += $data;
        } else {
            libxml_use_internal_errors(true);

            if (false !== $data = simplexml_load_string($input)) {
                parent::$data += (array)$data;
            }

            libxml_clear_errors();
        }

        unset($input, $data);
    }

    /**
     * Read OPTION data
     *
     * @return int
     */
    private static function read_opt(): int
    {
        /**
         * CLI options
         *
         * r/ret: Return option (Available in CLI executable mode only)
         * c/cmd: System commands (separated by "-" when multiple)
         * o/out: Output format (json/xml/nul, default: json, available when "r/ret" is set)
         * d/data: CLI Data package (Transfer to CGI progress)
         * p/pipe: CLI pipe data package (Transfer to CLI programs)
         * t/time: CLI read timeout (in microsecond, default: 0, wait till done)
         */
        //Get options
        if (empty($opt = getopt('c:o:d:p:t:r', ['cmd:', 'out:', 'data:', 'pipe', 'time:', 'ret'], $optind))) {
            return $optind;
        }

        //Get return option
        parent::$param_cli['ret'] = !empty(self::opt_val($opt, self::RET));

        //Get CMD value
        if (!empty($val = self::opt_val($opt, self::CMD)) && is_string($val['data'])) {
            parent::$data += [$val['key'] => data::decode($val['data'])];
        }

        //Get output value
        if (!empty($val = self::opt_val($opt, self::OUT)) && is_string($val['data'])) {
            parent::$data += [$val['key'] => &$val['data']];
        }

        //Get CGI data value
        if (!empty($val = self::opt_val($opt, self::DATA)) && is_string($val['data'])) {
            parent::$data += self::opt_data($val['data']);
        }

        //Get pipe data value
        if (!empty($val = self::opt_val($opt, self::PIPE)) && is_string($val['data'])) {
            parent::$param_cli['pipe'] = data::decode($val['data']);
        }

        //Get pipe timeout value
        if (!empty($val = self::opt_val($opt, self::TIME))) {
            parent::$param_cli['time'] = (int)$val['data'];
        }

        unset($opt, $val);
        return $optind;
    }

    /**
     * Read argument data
     *
     * @param int $optind
     */
    private static function read_argv(int $optind): void
    {
        //Extract arguments
        if (empty(parent::$param_cli['argv'] = array_slice($_SERVER['argv'], $optind))) {
            return;
        }

        //Recheck CMD
        empty($value = self::opt_val(parent::$data, self::CMD)) || !is_string($value['data'])
            ? parent::$data['cmd'] = data::decode(array_shift(parent::$param_cli['argv']))
            : parent::$data[$value['key']] = &$value['data'];

        unset($optind, $value);
    }

    /**
     * Extract values from options
     *
     * @param array $opt
     * @param array $keys
     *
     * @return array
     */
    private static function opt_val(array &$opt, array $keys): array
    {
        foreach ($keys as $key) {
            if (isset($opt[$key])) {
                $data = ['key' => &$key, 'data' => &$opt[$key]];

                unset($opt[$key], $keys, $key);
                return $data;
            }
        }

        unset($keys, $key);
        return [];
    }

    /**
     * Extract data from option
     *
     * @param string $value
     *
     * @return array
     */
    private static function opt_data(string $value): array
    {
        //Decode data in JSON/QUERY
        if (!is_array($data = json_decode(data::decode($value), true))) {
            parse_str($value, $data);
        }

        unset($value);
        return $data;
    }
}