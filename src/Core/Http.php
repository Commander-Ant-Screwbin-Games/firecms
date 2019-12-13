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

use Error;
use ParagonIE\CSPBuilder\CSPBuilder;

use function basename;
use function ctype_alnum;
use function file_get_contents;
use function header;
use function headers_sent;
use function htmlentities;
use function is_string;
use function json_encode;
use function preg_match;

/**
 * The http class.
 */
class Http implements Core
{
    /**
     * Send an ajax call.
     *
     * @param array $responseData The response data returned.
     * @param int   $responseCode The response code returned.
     *
     * @return void Returns nothing.
     */
    public function sendAjaxCall(array $responseData, int $responseCode): void
    {
        echo json_encode([
            'data' => $responseData,
            'code' => $responseCode,
        ]);
        exit;
    }

    /**
     * Validate a file name.
     *
     * @param string $file The file name to validate.
     *
     * @return mixed Returns false if the file name is invalid or returns a valid file name.
     */
    public function validFileName(string $file)
    {
        $file = basename($file);
        if (!ctype_alnum($file) || !preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $file)) {
            return false;
        }
        return $file;
    }

    /**
     * Escape any unwanted characters that could be harmful.
     *
     * @param string $outputData The data to escape.
     *
     * @return string Returns safe output to the client-side.
     */
    public function escapeOutput(string $outputData): string
    {
        return htmlentities($outputData, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Attempt to redirect the user to a new page.
     *
     * @param string $url          The url to redirect to.
     * @param int    $responseCode The custom response code to use.
     *
     * @return void Reutrns nothing.
     */
    public static function redirect(string $url, int $responseCode = 302): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url, true, $responseCode);
        } else {
            echo "<script>location.href='{$url}';</script>";
        }
        exit;
    }

    /**
     * Set who is powering this application.
     *
     * @return bool Returns true once the x-powered-by header has been sent.
     */
    public static function setXPoweredBy(): bool
    {
        header('X-Powered-By: FireCMS\Core');
        return true;
    }

    /**
     * Set a number of secure headers to enhance the application security.
     *
     * @return bool Returns true once all the headers have been sent.
     */
    public static function sendSecureHeaders(): bool
    {
        //----------------------------------------------------------------------------------------//
        // You want this header because it gives you fine-grained control over the internal       //
        // and external resources allowed to be loaded by the browser, which provides a           //
        // potent layer of defense against cross-site scripting vulnerabilities.                  //
        //----------------------------------------------------------------------------------------//
        $configuration = file_get_contents(__DIR__ . '/../../csp.json');
        if (!is_string($configuration)) {
            throw new Error('Could not read configuration file!');
        }
        $csp = CSPBuilder::fromData($configuration);
        $csp->sendCSPHeader();
        //----------------------------------------------------------------------------------------//
        // You want this header because it adds a layer of protection against                     //
        // rogue/compromised certificate authorities by forcing bad actors to publish             //
        // evidence of their mis-issued certificates to a publicly verifiable, append-only        //
        // data structure.                                                                        //
        //----------------------------------------------------------------------------------------//
        header('Expect-CT: enforce,max-age=30');
        //----------------------------------------------------------------------------------------//
        // You want this header because it allows you to control whether or not you               //
        // leak information about your users' behavior to third parties.                          //
        //----------------------------------------------------------------------------------------//
        header('Referrer-Policy: same-origin');
        //----------------------------------------------------------------------------------------//
        // You want this header because it tells browsers to force all future requests to         //
        // the same origin over HTTPS rather than insecure HTTP.                                  //
        //----------------------------------------------------------------------------------------//
        header('Strict-Transport-Security: max-age=30');
        //----------------------------------------------------------------------------------------//
        // You want this header because MIME type confusion can lead to unpredictable results,    //
        // including weird edge cases that allow XSS vulnerabilities.                             //
        //----------------------------------------------------------------------------------------//
        header('X-Content-Type-Options: nosniff');
        //----------------------------------------------------------------------------------------//
        // You want this header because it allows you to prevent clickjacking.                    //
        //----------------------------------------------------------------------------------------//
        header('X-Frame-Options: SAMEORIGIN');
        //----------------------------------------------------------------------------------------//
        // You want this header because it enables some browser anti-XSS features that are        //
        // not enabled by default.                                                                //
        //----------------------------------------------------------------------------------------//
        header('X-XSS-Protection: 1; mode=block');
        return true;
    }
}
