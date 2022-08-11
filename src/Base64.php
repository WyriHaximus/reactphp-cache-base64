<?php

declare(strict_types=1);

namespace WyriHaximus\React\Cache;

use React\Cache\CacheInterface;

use function base64_encode;
use function Safe\base64_decode;

final class Base64 implements CacheInterface
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function get($key, $default = null)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         * @psalm-suppress MissingClosureParamType
         */
        return $this->cache->get($key, $default)->then(static function ($result) use ($default): mixed {
            if ($result === null || $result === $default) {
                return $default;
            }

            /**
             * @psalm-suppress MixedArgument
             */
            return base64_decode($result);
        });
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function set($key, $value, $ttl = null)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         * @psalm-suppress MixedArgument
         * @phpstan-ignore-next-line
         */
        return $this->cache->set($key, base64_encode($value), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         */
        return $this->cache->delete($key);
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function getMultiple(array $keys, $default = null)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         * @psalm-suppress MissingClosureParamType
         */
        return $this->cache->getMultiple($keys, $default)->then(static function ($results) use ($default) {
            /**
             * @psalm-suppress MixedAssignment
             */
            foreach ($results as $key => $result) {
                if ($result === null || $result === $default) {
                    continue;
                }

                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress MixedArrayAssignment
                 * @psalm-suppress MixedArrayOffset
                 * @psalm-suppress MixedArgument
                 */
                $results[$key] = base64_decode($result);
            }

            return $results;
        });
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function setMultiple(array $values, $ttl = null)
    {
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedArgument
         */
        foreach ($values as $key => $value) {
            $values[$key] = base64_encode($value);
        }

        /**
         * @psalm-suppress TooManyTemplateParams
         */
        return $this->cache->setMultiple($values, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $keys)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         */
        return $this->cache->deleteMultiple($keys);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         */
        return $this->cache->clear();
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        /**
         * @psalm-suppress TooManyTemplateParams
         */
        return $this->cache->has($key);
    }
}
