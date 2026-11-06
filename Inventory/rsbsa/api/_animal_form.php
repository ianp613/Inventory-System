<?php
/**
 * "Animal Population Monitoring Form" (Municipality of Palo): the column layout, the grouping of the data per
 * barangay, and an .xlsx writer that reproduces the paper form (one sheet per barangay).
 * Needs PHP's zip extension; no Composer packages.
 */

/**
 * The columns of the form, in order. Each entry: [stored animal type, header text].
 * The stored animal type is what the Register form saves in parcel_livestock.animal_type.
 */
const ANIMAL_FORM_COLUMNS = [
    ['Carabao - Male', 'Male'],
    ['Carabao - Female (Caracow)', "Caracow\n(Female)"],
    ['Cattle - Male', 'Male'],
    ['Cattle - Female (Cow)', "Cow\n(Female)"],
    ['Swine - Boar (Male/Breeder)', "Boar\n(Male/\nBreeder)"],
    ['Swine - Sow (Inahin)', "Sow\n(Inahin)"],
    ['Swine - Fatteners (Patabain)', "Fatteners\n(Patabain)"],
    ['Swine - Piglets (Biik)', "Piglets\n(Biik)"],
    ['Goat - Male', 'Male'],
    ['Goat - Female', 'Female'],
    ['Sheep - Male', 'Male'],
    ['Sheep - Female', 'Female'],
    ['Gamefowl (Pangsabong)', "Gamefowl\n(Pangsabong)"],
    ['Chicken - Layer (Pang-itlog)', "Layer\n(Pang-\nitlog)"],
    ['Chicken - Native/Improved (for Dressing/Meat)', "Native/\nImproved\n(for\nDressing/\nMeat)"],
    ['Chicken - Broiler', 'Broiler'],
    ['Duck (Pato)', "Duck\n(Pato)"],
    ['Turkey (Pabo)', "Turkey\n(Pabo)"],
    ['Goose (Gansa)', "Goose\n(Gansa)"],
    ['Pigeon (Salapati)', "Pigeon\n(Salapati)"],
    ['Dog - Stray Male', '(M)'],
    ['Dog - Stray Female', '(F)'],
    ['Dog - Leashed Male', '(M)'],
    ['Dog - Leashed Female', '(F)'],
];

/**
 * The farmers that have an Animals parcel, with the heads per animal type, grouped by the barangay of the farm.
 * Only Active parcels are counted (Inactive / Deceased parcels are not part of the current population).
 *
 * @return array{groups: array<int, array{barangay:string, municipality:string, province:string, farmers:array}>, other_used: bool}
 *         one group per farm barangay + municipality; farmers sorted by name; counts: animal type => heads
 */
function animal_population(string $onlyBarangay = ''): array
{
    $where = "p.commodity = 'Animals' AND p.parcel_status = 'Active'";
    $args = [];
    if ($onlyBarangay !== '') {
        $where .= ' AND LOWER(p.loc_barangay) = LOWER(?)';
        $args[] = $onlyBarangay;
    }
    $parcels = q_all(
        "SELECT p.id, p.farmer_id, p.loc_barangay, p.loc_municipality, p.loc_province, f.surname, f.first_name, f.middle_name, f.suffix
           FROM parcels p JOIN farmers f ON f.id = p.farmer_id
          WHERE $where
          ORDER BY LOWER(p.loc_barangay), LOWER(p.loc_municipality), LOWER(f.surname), LOWER(f.first_name), p.farmer_id, p.id",
        $args
    );

    $known = array_column(ANIMAL_FORM_COLUMNS, 0);
    $heads = [];   // parcel id => [[type, heads], ...]
    if ($parcels) {
        $ids = array_column($parcels, 'id');
        foreach (array_chunk($ids, 500) as $chunk) {
            $in = implode(',', array_fill(0, count($chunk), '?'));
            foreach (q_all("SELECT parcel_id, animal_type, heads FROM parcel_livestock WHERE parcel_id IN ($in) ORDER BY id", $chunk) as $a) {
                $heads[(int) $a['parcel_id']][] = [$a['animal_type'], (int) $a['heads']];
            }
        }
    }

    $out = [];
    $otherUsed = false;
    foreach ($parcels as $p) {
        $b = trim((string) $p['loc_barangay']);
        $m = trim((string) $p['loc_municipality']);
        $key = mb_strtolower($b) . '|' . mb_strtolower($m);
        if (!isset($out[$key])) {
            $out[$key] = ['barangay' => $b, 'municipality' => $m, 'province' => trim((string) $p['loc_province']), 'farmers' => []];
        }
        $fid = (int) $p['farmer_id'];
        if (!isset($out[$key]['farmers'][$fid])) {
            $out[$key]['farmers'][$fid] = [
                'name' => person_name($p['surname'], $p['first_name'], $p['middle_name'], $p['suffix']),
                'counts' => [], 'other_counts' => [],
            ];
        }
        foreach ($heads[(int) $p['id']] ?? [] as [$type, $n]) {
            if (in_array($type, $known, true)) {
                $out[$key]['farmers'][$fid]['counts'][$type] = ($out[$key]['farmers'][$fid]['counts'][$type] ?? 0) + $n;
            } else {   // an older plain name (Cow, Swine, Poultry...) that has no column on the form
                $out[$key]['farmers'][$fid]['other_counts'][$type] = ($out[$key]['farmers'][$fid]['other_counts'][$type] ?? 0) + $n;
                $otherUsed = $otherUsed || $n > 0;
            }
        }
    }

    $groups = [];
    foreach ($out as $g) {
        $list = [];
        foreach ($g['farmers'] as $f) {
            $parts = [];
            foreach ($f['other_counts'] as $t => $n) {
                if ($n > 0) {
                    $parts[] = "$t: $n";
                }
            }
            $list[] = ['name' => $f['name'], 'counts' => $f['counts'], 'other' => implode('; ', $parts)];
        }
        $g['farmers'] = $list;
        $groups[] = $g;
    }
    usort($groups, fn($a, $b) => strnatcasecmp($a['barangay'], $b['barangay']) ?: strnatcasecmp($a['municipality'], $b['municipality']));
    return ['groups' => $groups, 'other_used' => $otherUsed];
}

/** Writes the form as an .xlsx: one sheet per barangay, landscape, header repeated on every printed page. */
class AnimalFormXlsx
{
    // cell styles (index into cellXfs)
    private const S_TITLE = 1, S_CENTER = 2, S_LEFT = 3, S_HEAD_B = 4, S_HEAD = 5, S_TEXT = 6, S_NUM = 7, S_ITALIC = 8, S_ITALIC_C = 9, S_LINE = 10;

    private const FIRST_COL = 0;    // A = name, B.. = the 24 animal columns
    private const HEADER_ROW = 6;   // first row of the column headings (1-based)
    private const MIN_ROWS = 20;    // blank writing lines are added up to this many rows

    /** @var array<int, array{name:string, xml:string}> */
    private array $sheets = [];

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

    private static function sheetName(string $name, array $used): string
    {
        $n = trim(preg_replace('/[\\\\\/?*\[\]:]/', ' ', $name) ?? '');
        $n = mb_substr($n === '' ? 'Form' : $n, 0, 31);
        $base = $n;
        for ($i = 2; in_array(mb_strtolower($n), $used, true); $i++) {
            $n = mb_substr($base, 0, 28) . ' ' . $i;
        }
        return $n;
    }

    /**
     * @param string $barangay   printed in capitals after "Barangay:" (empty = a line to write on)
     * @param string $municipality / $province  printed in capitals after "Municipality:" / "Province:" (municipality empty = a line to write on)
     * @param array  $farmers    [['name', 'counts' => [type => heads], 'other' => text], ...]
     * @param bool   $withOther  add the extra "older entries" column
     */
    public function addForm(string $barangay, array $farmers, string $asOf, bool $withOther, string $municipality = '', string $province = 'Leyte', int $minRows = self::MIN_ROWS, ?string $sheetTitle = null): void
    {
        $cells = [];     // row => col => [style, value|null, rich|null]
        $merges = [];
        $heights = [];
        $nCols = 1 + count(ANIMAL_FORM_COLUMNS) + ($withOther ? 1 : 0);
        $last = $nCols - 1;

        $put = function (int $r, int $c, $v, int $s, ?array $rich = null) use (&$cells) {
            $cells[$r][$c] = [$s, $v, $rich];
        };
        $merge = function (int $r1, int $c1, int $r2, int $c2, $v, int $s, ?array $rich = null) use (&$cells, &$merges, $put) {
            for ($r = $r1; $r <= $r2; $r++) {
                for ($c = $c1; $c <= $c2; $c++) {
                    $put($r, $c, null, $s);
                }
            }
            $put($r1, $c1, $v, $s, $rich);
            if ($r1 !== $r2 || $c1 !== $c2) {
                $merges[] = self::col($c1) . $r1 . ':' . self::col($c2) . $r2;
            }
        };

        // --- heading block
        $merge(1, 0, 1, $last, 'ANIMAL POPULATION MONITORING FORM', self::S_TITLE);
        $merge(2, 0, 2, $last, 'as of ' . $asOf, self::S_CENTER);
        $heights[1] = 22;
        $heights[3] = 8;
        $merge(4, 0, 4, 4, null, self::S_LEFT, [['Region: ', false], ['VIII', true]]);
        $caps = fn(string $v, string $blank) => trim($v) === '' ? $blank : mb_strtoupper(trim($v));
        $line = '______________________';
        $merge(5, 0, 5, 4, null, self::S_LEFT, [['Province: ', false], [$caps($province, $line), true]]);
        $mid1 = 9;
        $mid2 = min($last, 18);
        $merge(4, $mid1, 4, $mid2, null, self::S_CENTER, [['Municipality: ', false], [$caps($municipality, $line), true]]);
        $merge(5, $mid1, 5, $mid2, null, self::S_CENTER, [['Barangay: ', false], [$caps($barangay, $line), true]]);

        // --- column headings (4 rows), like the paper form
        $h = self::HEADER_ROW;          // group row
        $merge($h, 0, $h + 3, 0, 'Name of Farmer/Owner', self::S_HEAD_B);
        $groups = [
            [1, 2, 'Carabao'], [3, 4, 'Cattle (Baka)'], [5, 8, 'Swine (Baboy)'], [9, 10, 'Goat (Kanding)'],
            [11, 12, 'Sheep (Karnero)'], [13, 20, 'Poultry'], [21, 24, 'Dog (Ayam)'],
        ];
        foreach ($groups as [$c1, $c2, $label]) {
            $merge($h, $c1, $h, $c2, $label, self::S_HEAD_B);
        }
        // columns whose heading is just one tall cell
        $tall = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 17, 18, 19, 20];
        foreach ($tall as $c) {
            $merge($h + 1, $c, $h + 3, $c, ANIMAL_FORM_COLUMNS[$c - 1][1], self::S_HEAD);
        }
        // poultry: Chicken (Manok) > Native > Layer / Native-Improved; Gamefowl and Broiler sit beside Native
        $merge($h + 1, 13, $h + 1, 16, 'Chicken (Manok)', self::S_HEAD);
        $merge($h + 2, 14, $h + 2, 15, 'Native', self::S_HEAD);
        $merge($h + 2, 13, $h + 3, 13, ANIMAL_FORM_COLUMNS[12][1], self::S_HEAD);
        $merge($h + 2, 16, $h + 3, 16, ANIMAL_FORM_COLUMNS[15][1], self::S_HEAD);
        $merge($h + 3, 14, $h + 3, 14, ANIMAL_FORM_COLUMNS[13][1], self::S_HEAD);
        $merge($h + 3, 15, $h + 3, 15, ANIMAL_FORM_COLUMNS[14][1], self::S_HEAD);
        // dogs: Stray (Layaw) / Leashed (Nakahigot) > (M) (F)
        $merge($h + 1, 21, $h + 1, 22, "Stray\n(Layaw)", self::S_HEAD);
        $merge($h + 1, 23, $h + 1, 24, "Leashed\n(Nakahigot)", self::S_HEAD);
        foreach ([21, 22, 23, 24] as $c) {
            $merge($h + 2, $c, $h + 3, $c, ANIMAL_FORM_COLUMNS[$c - 1][1], self::S_HEAD);
        }
        if ($withOther) {
            $merge($h, 25, $h + 3, 25, "Not specified\n(older entries)", self::S_HEAD_B);
        }
        $heights[$h] = 20;
        $heights[$h + 1] = 28;
        $heights[$h + 2] = 28;
        $heights[$h + 3] = 66;

        // --- rows of farmers (blank lines are added so there is room to write by hand)
        $r = $h + 4;
        $rows = max($minRows, count($farmers));
        for ($i = 0; $i < $rows; $i++, $r++) {
            $f = $farmers[$i] ?? null;
            $put($r, 0, $f['name'] ?? null, self::S_TEXT);
            foreach (ANIMAL_FORM_COLUMNS as $ci => [$type]) {
                $n = $f['counts'][$type] ?? 0;
                $put($r, $ci + 1, $n > 0 ? $n : null, self::S_NUM);
            }
            if ($withOther) {
                $put($r, 25, $f['other'] ?? null, self::S_TEXT);
            }
            $heights[$r] = 18;
        }
        $lastData = $r - 1;

        // --- signatures
        $r += 1;
        $merge($r, 0, $r, 4, 'Prepared and Submitted by:', self::S_ITALIC);
        $merge($r, 12, $r, 17, 'Noted by:', self::S_ITALIC);
        $r += 3;
        $merge($r, 2, $r, 6, null, self::S_LINE);
        $merge($r, 13, $r, 18, null, self::S_LINE);
        $r += 1;
        $merge($r, 2, $r, 6, 'Kagawad on Agriculture', self::S_ITALIC_C);
        $merge($r, 13, $r, 18, 'Barangay Chairman', self::S_ITALIC_C);
        $lastRow = $r;

        // --- xml
        $x = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
           . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
           . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
           . '<dimension ref="A1:' . self::col($last) . $lastRow . '"/>'
           . '<sheetViews><sheetView workbookViewId="0" showGridLines="0"><pane ySplit="' . ($h + 3) . '" topLeftCell="A' . ($h + 4) . '" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
           . '<sheetFormatPr defaultRowHeight="15"/>'
           . '<cols><col min="1" max="1" width="30" customWidth="1"/>'
           . '<col min="2" max="13" width="10.5" customWidth="1"/>'
           . '<col min="14" max="14" width="13" customWidth="1"/>'          // Gamefowl (Pangsabong)
           . '<col min="15" max="' . (1 + count(ANIMAL_FORM_COLUMNS)) . '" width="10.5" customWidth="1"/>'
           . ($withOther ? '<col min="26" max="26" width="36" customWidth="1"/>' : '') . '</cols>'
           . '<sheetData>';
        ksort($cells);
        foreach ($cells as $row => $cols) {
            ksort($cols);
            $x .= '<row r="' . $row . '"' . (isset($heights[$row]) ? ' ht="' . $heights[$row] . '" customHeight="1"' : '') . '>';
            foreach ($cols as $c => [$s, $v, $rich]) {
                $ref = self::col($c) . $row;
                if ($rich) {
                    $x .= '<c r="' . $ref . '" s="' . $s . '" t="inlineStr"><is>';
                    foreach ($rich as [$text, $bold]) {
                        if ($text === '') {
                            continue;
                        }
                        $x .= '<r>' . ($bold ? '<rPr><b/><sz val="10"/><rFont val="Calibri"/></rPr>' : '<rPr><sz val="10"/><rFont val="Calibri"/></rPr>')
                            . '<t xml:space="preserve">' . self::esc($text) . '</t></r>';
                    }
                    $x .= '</is></c>';
                } elseif ($v === null || $v === '') {
                    $x .= '<c r="' . $ref . '" s="' . $s . '"/>';
                } elseif (is_int($v) || is_float($v)) {
                    $x .= '<c r="' . $ref . '" s="' . $s . '"><v>' . $v . '</v></c>';
                } else {
                    $x .= '<c r="' . $ref . '" s="' . $s . '" t="inlineStr"><is><t xml:space="preserve">' . self::esc((string) $v) . '</t></is></c>';
                }
            }
            $x .= '</row>';
        }
        $x .= '</sheetData><mergeCells count="' . count($merges) . '">';
        foreach ($merges as $m) {
            $x .= '<mergeCell ref="' . $m . '"/>';
        }
        $x .= '</mergeCells>'
            . '<printOptions horizontalCentered="1"/>'
            . '<pageMargins left="0.3" right="0.3" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>'
            . '<pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
            . '</worksheet>';

        $this->sheets[] = ['name' => $sheetTitle ?? $barangay, 'xml' => $x, 'titleRows' => [$h, $h + 3]];
    }

    private function stylesXml(): string
    {
        $thin = '<left style="thin"><color rgb="FF000000"/></left><right style="thin"><color rgb="FF000000"/></right><top style="thin"><color rgb="FF000000"/></top><bottom style="thin"><color rgb="FF000000"/></bottom><diagonal/>';
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="4">'
            .   '<font><sz val="10"/><name val="Calibri"/></font>'
            .   '<font><b/><sz val="10"/><name val="Calibri"/></font>'
            .   '<font><b/><sz val="13"/><name val="Calibri"/></font>'
            .   '<font><i/><sz val="10"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="3"><border><left/><right/><top/><bottom/><diagonal/></border>'
            .   '<border>' . $thin . '</border>'
            .   '<border><left/><right/><top/><bottom style="thin"><color rgb="FF000000"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="11">'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .   '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="1" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>'
            .   '<xf numFmtId="1" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="2" xfId="0" applyBorder="1"/>'
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
        if (!$this->sheets) {
            throw new RuntimeException('No form to write.');
        }
        $path = tempnam(sys_get_temp_dir(), 'rsbsa');
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the Excel file.');
        }

        $names = [];
        $sheetsXml = $rels = $overrides = $titles = '';
        foreach ($this->sheets as $i => $s) {
            $n = self::sheetName($s['name'] === '' ? 'Form' : $s['name'], $names);
            $names[] = mb_strtolower($n);
            $id = $i + 1;
            $sheetsXml .= '<sheet name="' . self::esc($n) . '" sheetId="' . $id . '" r:id="rId' . $id . '"/>';
            $rels .= '<Relationship Id="rId' . $id . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $id . '.xml"/>';
            $overrides .= '<Override PartName="/xl/worksheets/sheet' . $id . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $titles .= '<definedName name="_xlnm.Print_Titles" localSheetId="' . $i . '">\'' . self::esc(str_replace("'", "''", $n)) . '\'!$' . $s['titleRows'][0] . ':$' . $s['titleRows'][1] . '</definedName>';
            $zip->addFromString('xl/worksheets/sheet' . $id . '.xml', $s['xml']);
        }
        $styleRel = 'rId' . (count($this->sheets) + 1);

        $zip->addFromString('[Content_Types].xml',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . $overrides
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
            . '<sheets>' . $sheetsXml . '</sheets><definedNames>' . $titles . '</definedNames></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rels
            . '<Relationship Id="' . $styleRel . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->close();

        return $path;
    }
}
