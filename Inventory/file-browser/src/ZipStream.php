<?php
declare(strict_types=1);

/**
 * Minimal streaming ZIP writer (method "store", i.e. no compression).
 *
 * - Nothing is written to disk and nothing is held in memory, so a multi-GB folder
 *   starts downloading immediately (no proxy timeout while a zip file is being built).
 * - Uses data descriptors, so the CRC is computed while the file streams out.
 * - ZIP64 is used automatically for files or archives that need it.
 */
final class ZipStream
{
    /** Values at or above this need ZIP64 (public so tests can lower them). */
    public static int $limit = 0xFFFFFFFF;
    public static int $maxEntries = 0xFFFF;

    private int $offset = 0;
    /** @var array<int,array<string,mixed>> */
    private array $central = [];

    private static function dos(int $ts): array
    {
        $d = getdate($ts > 0 ? $ts : time());
        if ($d['year'] < 1980) {
            return [0, (1 << 5) | 1];
        }
        return [
            ($d['hours'] << 11) | ($d['minutes'] << 5) | intdiv($d['seconds'], 2),
            (($d['year'] - 1980) << 9) | ($d['mon'] << 5) | $d['mday'],
        ];
    }

    private static function utf8(string $s): string
    {
        return mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }

    private function out(string $data): void
    {
        echo $data;
        $this->offset += strlen($data);
        if (connection_aborted()) {
            throw new RuntimeException('Download cancelled.');
        }
    }

    public function addDir(string $name, int $mtime): void
    {
        $name = rtrim(self::utf8($name), '/') . '/';
        [$time, $date] = self::dos($mtime);
        $off = $this->offset;
        $this->out(pack('VvvvvvVVVvv', 0x04034b50, 20, 0x0800, 0, $time, $date, 0, 0, 0, strlen($name), 0) . $name);
        $this->central[] = ['name' => $name, 'flags' => 0x0800, 'time' => $time, 'date' => $date,
            'crc' => 0, 'size' => 0, 'off' => $off, 'z64' => false, 'dir' => true];
    }

    public function addFile(string $abs, string $name, int $size, int $mtime): void
    {
        $fh = @fopen($abs, 'rb');
        if (!$fh) {
            return; // unreadable (locked / no permission): skip it rather than break the whole zip
        }
        $name = self::utf8($name);
        [$time, $date] = self::dos($mtime);
        $z64 = $size >= self::$limit;
        $flags = 0x0808; // bit 3: sizes/CRC follow the data; bit 11: UTF-8 names
        $extra = $z64 ? pack('vvPP', 1, 16, 0, 0) : '';
        $off = $this->offset;

        $this->out(pack(
            'VvvvvvVVVvv',
            0x04034b50, $z64 ? 45 : 20, $flags, 0, $time, $date,
            0, $z64 ? 0xFFFFFFFF : 0, $z64 ? 0xFFFFFFFF : 0,
            strlen($name), strlen($extra)
        ) . $name . $extra);

        $ctx = hash_init('crc32b');
        $len = 0;
        while (!feof($fh)) {
            $buf = fread($fh, 1048576);
            if ($buf === false || $buf === '') {
                break;
            }
            hash_update($ctx, $buf);
            $len += strlen($buf);
            $this->out($buf);
            flush();
        }
        fclose($fh);
        $crc = hexdec(hash_final($ctx));

        if ($len >= self::$limit && !$z64) {
            throw new RuntimeException('"' . $name . '" grew past 4 GB while it was being zipped.');
        }
        $this->out($z64
            ? pack('VVPP', 0x08074b50, $crc, $len, $len)
            : pack('VVVV', 0x08074b50, $crc, $len, $len));

        $this->central[] = ['name' => $name, 'flags' => $flags, 'time' => $time, 'date' => $date,
            'crc' => $crc, 'size' => $len, 'off' => $off, 'z64' => $z64, 'dir' => false];
    }

    public function finish(): void
    {
        $cdStart = $this->offset;
        foreach ($this->central as $e) {
            $bigOff = $e['off'] >= self::$limit;
            $needs64 = $e['z64'] || $bigOff;
            $data = '';
            if ($e['z64']) {
                $data .= pack('PP', $e['size'], $e['size']);
            }
            if ($bigOff) {
                $data .= pack('P', $e['off']);
            }
            $extra = $needs64 ? pack('vv', 1, strlen($data)) . $data : '';
            $this->out(pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                $needs64 ? 0x032D : 0x0314,
                $needs64 ? 45 : 20,
                $e['flags'], 0, $e['time'], $e['date'],
                $e['crc'],
                $e['z64'] ? 0xFFFFFFFF : $e['size'],
                $e['z64'] ? 0xFFFFFFFF : $e['size'],
                strlen($e['name']), strlen($extra), 0, 0, 0,
                $e['dir'] ? ((0040755 << 16) | 0x10) : (0100644 << 16),
                $bigOff ? 0xFFFFFFFF : $e['off']
            ) . $e['name'] . $extra);
        }

        $n = count($this->central);
        $cdSize = $this->offset - $cdStart;

        if ($n >= self::$maxEntries || $cdStart >= self::$limit || $cdSize >= self::$limit) {
            $z64Off = $this->offset;
            $this->out(pack('VPvvVVPPPP', 0x06064b50, 44, 45, 45, 0, 0, $n, $n, $cdSize, $cdStart));
            $this->out(pack('VVPV', 0x07064b50, 0, $z64Off, 1));
            $this->out(pack('VvvvvVVv', 0x06054b50, 0, 0, 0xFFFF, 0xFFFF, 0xFFFFFFFF, 0xFFFFFFFF, 0));
        } else {
            $this->out(pack('VvvvvVVv', 0x06054b50, 0, 0, $n, $n, $cdSize, $cdStart, 0));
        }
        flush();
    }
}
