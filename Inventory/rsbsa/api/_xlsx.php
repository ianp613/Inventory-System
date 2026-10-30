<?php
/**
 * Minimal .xlsx writer (needs PHP's zip extension, no Composer packages).
 * One sheet, bold header, borders, frozen header row, landscape fit-to-width printing,
 * and the header row repeats on every printed page.
 */
class SimpleXlsx
{
    // cell styles (index into cellXfs in styles.xml)
    public const S_DEFAULT = 0;
    public const S_HEADER  = 1;
    public const S_TITLE   = 2;
    public const S_TEXT    = 3;
    public const S_NUMBER  = 4;
    public const S_SUBTITLE = 5;

    private array $rows = [];
    private array $widths = [];
    private int $headerRow = 0;
    private string $sheetName;

    public function __construct(string $sheetName = 'Report')
    {
        $this->sheetName = preg_replace('/[\\\\\/?*\[\]:]/', ' ', $sheetName) ?: 'Report';
    }

    /** @param array $cells values (string|int|float|null) @param int|null $style one style for the whole row */
    public function addRow(array $cells, ?int $style = null): int
    {
        $this->rows[] = ['cells' => array_values($cells), 'style' => $style];
        return count($this->rows);
    }

    public function addBlankRow(): void
    {
        $this->rows[] = ['cells' => [], 'style' => null];
    }

    /** Mark the (1-based) row that holds the column titles: frozen and repeated when printing. */
    public function setHeaderRow(int $row): void
    {
        $this->headerRow = $row;
    }

    public function setColumnWidths(array $widths): void
    {
        $this->widths = array_values($widths);
    }

    private static function col(int $i): string
    {
        $s = '';
        for ($i++; $i > 0; $i = intdiv($i - 1, 26)) {
            $s = chr(65 + ($i - 1) % 26) . $s;
        }
        return $s;
    }

    private static function esc(string $s): string
    {
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $s) ?? '';
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function sheetXml(): string
    {
        $cols = 1;
        foreach ($this->rows as $r) {
            $cols = max($cols, count($r['cells']));
        }

        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
           . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
           . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
           . '<dimension ref="A1:' . self::col($cols - 1) . max(1, count($this->rows)) . '"/>';

        $x .= '<sheetViews><sheetView workbookViewId="0">';
        if ($this->headerRow > 0) {
            $x .= '<pane ySplit="' . $this->headerRow . '" topLeftCell="A' . ($this->headerRow + 1) . '" activePane="bottomLeft" state="frozen"/>';
        }
        $x .= '</sheetView></sheetViews><sheetFormatPr defaultRowHeight="15"/>';

        if ($this->widths) {
            $x .= '<cols>';
            foreach ($this->widths as $i => $w) {
                $x .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . (float) $w . '" customWidth="1"/>';
            }
            $x .= '</cols>';
        }

        $x .= '<sheetData>';
        foreach ($this->rows as $ri => $row) {
            $r = $ri + 1;
            $x .= '<row r="' . $r . '">';
            foreach ($row['cells'] as $ci => $v) {
                if ($v === null || $v === '') {
                    // keep empty cells in the table so the borders stay continuous
                    $style = $row['style'] ?? self::S_TEXT;
                    if ($row['style'] === null && $r <= $this->headerRow) {
                        $style = self::S_DEFAULT;
                    }
                    $x .= '<c r="' . self::col($ci) . $r . '" s="' . $style . '"/>';
                    continue;
                }
                $isNum = is_int($v) || is_float($v);
                $style = $row['style'] ?? ($isNum ? self::S_NUMBER : self::S_TEXT);
                $ref = self::col($ci) . $r;
                if ($isNum) {
                    $x .= '<c r="' . $ref . '" s="' . $style . '"><v>' . $v . '</v></c>';
                } else {
                    $x .= '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">'
                        . self::esc((string) $v) . '</t></is></c>';
                }
            }
            $x .= '</row>';
        }
        $x .= '</sheetData>'
            . '<pageMargins left="0.4" right="0.4" top="0.6" bottom="0.6" header="0.3" footer="0.3"/>'
            . '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
            . '<headerFooter><oddFooter>&amp;LPalo RSBSA&amp;CPage &amp;P of &amp;N&amp;R&amp;D</oddFooter></headerFooter>'
            . '</worksheet>';
        return $x;
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="1"><numFmt numFmtId="164" formatCode="0.00##"/></numFmts>'
            . '<fonts count="4">'
            .   '<font><sz val="11"/><name val="Calibri"/></font>'
            .   '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .   '<font><b/><sz val="14"/><name val="Calibri"/></font>'
            .   '<font><i/><sz val="10"/><color rgb="FF555555"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .   '<fill><patternFill patternType="solid"><fgColor rgb="FF1F6B38"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'
            .   '<border><left style="thin"><color rgb="FFBBBBBB"/></left><right style="thin"><color rgb="FFBBBBBB"/></right>'
            .   '<top style="thin"><color rgb="FFBBBBBB"/></top><bottom style="thin"><color rgb="FFBBBBBB"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="6">'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .   '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            .   '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf>'
            .   '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment vertical="top"/></xf>'
            .   '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    /** Build the file and return its path (a temp file the caller must delete). */
    public function save(): string
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('The PHP zip extension is required for Excel export.');
        }
        $path = tempnam(sys_get_temp_dir(), 'rsbsa');
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the Excel file.');
        }

        $print = $this->headerRow > 0
            ? '<definedNames><definedName name="_xlnm.Print_Titles" localSheetId="0">\'' . self::esc($this->sheetName) . '\'!$' . $this->headerRow . ':$' . $this->headerRow . '</definedName></definedNames>'
            : '';

        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>');
        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/workbook.xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::esc(mb_substr($this->sheetName, 0, 31)) . '" sheetId="1" r:id="rId1"/></sheets>' . $print . '</workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml());
        $zip->close();

        return $path;
    }
}
