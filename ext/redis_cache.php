<?php

/**
 * Redis Cache Extension
 *
 * Copyright 2018 秋水之冰 <27206617@qq.com>
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

class redis_cache extends redis
{
    //Cache key prefix
    const PREFIX = 'CAS:';

    /**
     * Set cache
     *
     * @param string $key
     * @param array  $data
     * @param int    $life
     *
     * @return bool
     * @throws \RedisException
     */
    public function set(string $key, array $data, int $life = 600): bool
    {
        $key   = self::PREFIX . $key;
        $cache = json_encode($data);

        $result = 0 < $life ? parent::connect()->set($key, $cache, $life) : parent::connect()->set($key, $cache);

        unset($key, $data, $life, $cache);
        return $result;
    }

    /**
     * Get cache
     *
     * @param string $key
     *
     * @return array
     * @throws \RedisException
     */
    public function get(string $key): array
    {
        $cache = parent::connect()->get(self::PREFIX . $key);

        if (false === $cache) {
            return [];
        }

        $data = json_decode($cache, true);

        if (!is_array($data)) {
            return [];
        }

        unset($key, $cache);
        return $data;
    }

    /**
     * Delete cache
     *
     * @param string $key
     *
     * @return int
     * @throws \RedisException
     */
    public function del(string $key): int
    {
        $result = parent::connect()->del(self::PREFIX . $key);

        unset($key);
        return $result;
    }
}