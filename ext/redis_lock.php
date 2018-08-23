<?php

/**
 * Redis Lock Extension
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

class redis_lock extends redis
{
    //Lock sets
    private $locks = [];

    //Lock key prefix
    const PREFIX = 'LOCK:';

    //Retry properties
    const WAIT  = 1000;
    const RETRY = 10;

    /**
     * Lock on
     *
     * @param string $key
     * @param int    $life
     *
     * @return bool
     * @throws \RedisException
     */
    public function on(string $key, int $life = 3): bool
    {
        $retry = 0;
        $key   = self::PREFIX . $key;

        while ($retry <= self::RETRY) {
            if ($this->lock($key, $life)) {
                register_shutdown_function([$this, 'clear']);

                unset($key, $life, $retry);
                return true;
            }

            usleep(self::WAIT);
            ++$retry;
        }

        unset($key, $life, $retry);
        return false;
    }

    /**
     * Lock off
     *
     * @param string $key
     *
     * @throws \RedisException
     */
    public function off(string $key): void
    {
        $key = self::PREFIX . $key;
        parent::connect()->del($key);

        if (false !== $key = array_search($key, $this->locks, true)) {
            unset($this->locks[$key]);
        }

        unset($key);
    }

    /**
     * Clear all locks
     *
     * @throws \RedisException
     */
    public function clear(): void
    {
        if (!empty($this->locks)) {
            call_user_func_array([parent::connect(), 'del'], $this->locks);
            $this->locks = [];
        }
    }

    /**
     * Set lock
     *
     * @param string $key
     * @param int    $life
     *
     * @return bool
     * @throws \RedisException
     */
    private function lock(string $key, int $life): bool
    {
        $redis = parent::connect();

        if (!$redis->setnx($key, time())) {
            return false;
        }

        $redis->expire($key, 0 < $life ? $life : 3);
        $this->locks[] = &$key;

        unset($key, $life, $redis);
        return true;
    }
}