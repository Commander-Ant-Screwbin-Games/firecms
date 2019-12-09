<?php declare(strict_types=1);
/**
 * Fire Content Management System - A simple and secure piece of art.
 *
 * @license MIT License. (https://github.com/Commander-Ant-Screwbin-Games/firecms/blob/master/license)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * https://github.com/Commander-Ant-Screwbin-Games/firecms/tree/master/src/Core
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package Commander-Ant-Screwbin-Games/firecms.
 */

namespace FireCMS\Core;

use Symfony\Component\OptionsResolver\OptionsResolver;

use function password_hash;
use function password_needs_rehash;
use function password_verify;

/**
 * The password hash class.
 */
class PasswordHash implements Core
{

    /** @var array $options The password hash options. */
    private $options = [];

    /**
     * Construct a new password hash class.
     *
     * @param array $options The password hash options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Hash a new password.
     *
     * @param string $password The password to hash.
     *
     * @return string Returns the hashed password.
     */
    public function hash(string $password): string
    {
        return password_hash($this->options['algo'], $password, $this->options);
    }

    /**
     * Check to see if the hash needs a rehash.
     *
     * @param string $hash The hash to check.
     *
     * @return bool Returns true if the hash needs to be rehashed.
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->options['algo'], $this->options);
    }

    /**
     * Verify the password matches the hash.
     *
     * @param string $password The password to check.
     * @param string $hash     The hash to check against.
     *
     * @return bool Returns true if the password matches the hash and false if not.
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Configure the password hash options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The options resolver.
     *
     * @return void Returns nothing.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'algo' => PASSWORD_DEFAULT,
            'cost' => 10,
        ]);
        $resolver->setAllowedTypes('algo', 'int');
        $resolver->setAllowedTypes('cost', 'int');
        $resolver->setAllowedTypes('memory_cost', 'int');
        $resolver->setAllowedTypes('time_cost', 'int');
        $resolver->setAllowedTypes('threads', 'int');
    }
}
