<?php
/**
 * Copyright (c) 2008 Invest-In-France Agency http://www.invest-in-france.org
 *
 * Author : Thomas Rabaix
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

namespace CakeDC\NavAuth\Network;

use Cake\Core\Configure;

/**
 * Class NTLMStream
 */
class NTLMStream
{
    /**
     * Path
     */
    protected $path;

    /**
     * Mode
     */
    protected $mode;

    /**
     * Options
     */
    protected $options;

    /**
     * Opened path
     */
    protected $opened_path;

    /**
     * Buffer
     */
    protected $buffer;

    /**
     * Pos
     */
    protected $pos;

    /**
     * Ch
     */
    protected $ch;

    /**
     * Open stream
     *
     * @param string $path Path
     * @param string $mode Mode
     * @param array $options Options
     * @param string $opened_path Opened path
     *
     * @return bool
     */
    // phpcs:ignore
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->opened_path = $opened_path;
        $this->createBuffer($path);

        return true;
    }
    /**
     * Close the stream
     *
     * @return void
     */
    // phpcs:ignore
    public function stream_close()
    {
        curl_close($this->ch);
    }
    /**
     * Read the stream
     *
     * @param int $count number of bytes to read
     * @return string|bool content from pos to count
     */
    // phpcs:ignore
    public function stream_read($count)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }
        $read = substr($this->buffer, $this->pos, $count);
        $this->pos += $count;

        return $read;
    }

    /**
     * Write the stream
     *
     * @param string $data Data to be written
     * @return bool
     */
    // phpcs:ignore
    public function stream_write($data)
    {
        if (strlen($this->buffer) == 0) {
            return false;
        }

        return true;
    }
    /**
     *
     * @return true if eof else false
     */
    // phpcs:ignore
    public function stream_eof()
    {
        return ($this->pos > strlen($this->buffer));
    }
    /**
     * @return int the position of the current read pointer
     */
    // phpcs:ignore
    public function stream_tell()
    {
        return $this->pos;
    }
    /**
     * Flush stream data
     *
     * @return void
     */
    // phpcs:ignore
    public function stream_flush()
    {
        $this->buffer = null;
        $this->pos = null;
    }
    /**
     * Stat the file, return only the size of the buffer
     *
     * @return array stat information
     */
    // phpcs:ignore
    public function stream_stat()
    {
        $this->createBuffer($this->path);
        $stat = [
            'size' => strlen($this->buffer),
        ];

        return $stat;
    }
    /**
     * Stat the url, return only the size of the buffer
     *
     * @param string $path Path to create buffer
     * @param mixed $flags Flags
     *
     * @return array stat information
     */
    // phpcs:ignore
    public function url_stat($path, $flags)
    {
        $this->createBuffer($path);
        $stat = [
            'size' => strlen($this->buffer),
        ];

        return $stat;
    }
    /**
     * Create the buffer by requesting the url through cURL
     *
     * @param string $path Path to create buffer
     *
     * @return void
     */
    protected function createBuffer($path)
    {
        if ($this->buffer) {
            return;
        }
        //TODO Refactor this using Http Client after implementing NTLM authenticate in CakePHP core
        $this->ch = curl_init($path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36');
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($this->ch, CURLOPT_FAILONERROR, 1);
        curl_setopt(
            $this->ch,
            CURLOPT_USERPWD,
            sprintf(
                '%s\\%s:%s',
                Configure::read('NavAuth.auth.ntlm.domain'),
                Configure::read('NavAuth.auth.ntlm.username'),
                Configure::read('NavAuth.auth.ntlm.password')
            )
        );
        $this->buffer = curl_exec($this->ch);
        $this->pos = 0;
    }
}
