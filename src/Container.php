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

namespace FireCMS;

/**
 * Be able to manage this container statically.
 */
class Container
{
    /** @var mixed $container The app container. */
    private static $container = null;

    /**
     * Load the the app container.
     *
     * @param mixed $appContainer The container to make static.
     *
     * @return void Returns nothing.
     */
    public static function setContainer($appContainer): void
    {
        static::$container = $appContainer;
    }

    /**
     * Get the static container.
     *
     * @return mixed Returns the app container.
     */
    public static function get()
    {
        return static::$container;
    }
}
