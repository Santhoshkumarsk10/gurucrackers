<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use ZipArchive;

class ProductImportService
{
    /**
     * Parse and import products from an XLSX or CSV file.
     *
     * @param string $filePath
     * @param string $extension
     * @param bool $dryRun If true, parse and validate without saving to database
     * @param bool $updateExisting If true, update products that match by name & category
     * @return array Summary of import operations
     */
    public function import(string $filePath, string $extension, bool $dryRun = false, bool $updateExisting = true): array
    {
        $rows = $this->extractRows($filePath, $extension);
        return $this->processRows($rows, $dryRun, $updateExisting);
    }

    /**
     * Extract raw rows from XLSX or CSV file into standard column arrays.
     */
    public function extractRows(string $filePath, string $extension): array
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->extractFromXlsx($filePath);
        }

        return $this->extractFromCsv($filePath);
    }

    /**
     * Parse XLSX file natively using ZipArchive and SimpleXML (zero external dependencies).
     */
    protected function extractFromXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new \Exception("Unable to open the Excel file. Please ensure it is a valid .xlsx file.");
        }

        // 1. Read shared strings if present
        $sharedStrings = [];
        if ($zip->locateName('xl/sharedStrings.xml') !== false) {
            $xmlContent = $zip->getFromName('xl/sharedStrings.xml');
            if ($xmlContent) {
                $xml = simplexml_load_string($xmlContent);
                if ($xml && isset($xml->si)) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string) $si->t;
                        } elseif (isset($si->r)) {
                            $textParts = [];
                            foreach ($si->r as $run) {
                                if (isset($run->t)) {
                                    $textParts[] = (string) $run->t;
                                }
                            }
                            $sharedStrings[] = implode('', $textParts);
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            }
        }

        // 2. Read first sheet
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXmlContent) {
            // Try alternative sheet name
            $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet.xml');
        }

        if (!$sheetXmlContent) {
            $zip->close();
            throw new \Exception("Could not find worksheet data in Excel file.");
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        $rows = [];

        if ($sheetXml && isset($sheetXml->sheetData->row)) {
            foreach ($sheetXml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $c) {
                    $col = preg_replace('/[0-9]/', '', (string) $c['r']);
                    $type = (string) $c['t'];
                    $val = (string) $c->v;

                    if ($type === 's') {
                        $val = $sharedStrings[(int) $val] ?? $val;
                    } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                        $val = (string) $c->is->t;
                    }

                    $rowData[$col] = trim($val);
                }

                if (!empty(array_filter($rowData))) {
                    $rows[] = $rowData;
                }
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Parse CSV file into standard column arrays.
     */
    protected function extractFromCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                $rowData = [
                    'A' => isset($data[0]) ? trim($data[0]) : '',
                    'B' => isset($data[1]) ? trim($data[1]) : '',
                    'C' => isset($data[2]) ? trim($data[2]) : '',
                    'D' => isset($data[3]) ? trim($data[3]) : '',
                ];
                if (!empty(array_filter($rowData))) {
                    $rows[] = $rowData;
                }
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Process parsed rows matching the Sivakasi Cracker Price List format:
     * - Header: S.No | Name of the Product | Per | Actual Rate
     * - Category Row: e.g. ['ONE SOUNT CRACKERS', '', '', '']
     * - Product Row: e.g. ['1', 'Gold Lakshmi', '1 Pkt', '320']
     */
    public function processRows(array $rows, bool $dryRun = false, bool $updateExisting = true): array
    {
        $shop = Shop::current();
        $companyDiscount = ($shop && $shop->offer_percentage !== null) ? (int) $shop->offer_percentage : 90;

        $stats = [
            'total_rows_parsed' => count($rows),
            'categories_detected' => 0,
            'categories_created' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'products_skipped' => 0,
            'errors' => [],
            'preview_items' => [],
        ];

        $currentCategoryName = 'General Crackers';
        $categoryMap = []; // cache existing categories by normalized name
        $existingCategories = Category::all();
        foreach ($existingCategories as $cat) {
            $categoryMap[mb_strtoupper(trim($cat->name))] = $cat;
        }

        $sortOrder = Category::max('sort_order') ?? 0;

        foreach ($rows as $rowIndex => $row) {
            $colA = $row['A'] ?? '';
            $colB = $row['B'] ?? '';
            $colC = $row['C'] ?? '';
            $colD = $row['D'] ?? '';

            // 1. Skip header row
            if (
                stripos($colA, 's.no') !== false ||
                stripos($colB, 'name of the product') !== false ||
                stripos($colB, 'product name') !== false
            ) {
                continue;
            }

            // 2. Detect Category Header Row
            // In Sivakasi format, Category row has name in Col A and no price in Col D or Col C
            if (!empty($colA) && empty($colD) && empty($colC) && !is_numeric($colA)) {
                $currentCategoryName = trim($colA);
                $stats['categories_detected']++;
                continue;
            }

            // Also check if Category is in Col B with other columns empty
            if (empty($colA) && !empty($colB) && empty($colC) && empty($colD)) {
                $currentCategoryName = trim($colB);
                $stats['categories_detected']++;
                continue;
            }

            // 3. Detect Product Row
            $productName = '';
            $unit = '1 Box';
            $actualRateRaw = '';

            if (!empty($colB) && !empty($colD)) {
                // Standard format: A=S.No, B=Product Name, C=Unit, D=Actual Rate
                $productName = trim($colB);
                $unit = !empty($colC) ? trim($colC) : '1 Box';
                $actualRateRaw = $colD;
            } elseif (!empty($colA) && !empty($colC) && is_numeric(str_replace(',', '', $colC))) {
                // 3-column format: A=Product Name, B=Unit, C=Actual Rate
                $productName = trim($colA);
                $unit = !empty($colB) ? trim($colB) : '1 Box';
                $actualRateRaw = $colC;
            }

            if (empty($productName)) {
                continue;
            }

            // Clean rate
            $cleanRateStr = preg_replace('/[^0-9\.]/', '', $actualRateRaw);
            if (!is_numeric($cleanRateStr) || (float) $cleanRateStr <= 0) {
                continue;
            }

            $actualRate = (float) $cleanRateStr;
            $netRate = round($actualRate * (1 - ($companyDiscount / 100)), 2);

            if ($dryRun) {
                if (count($stats['preview_items']) < 15) {
                    $stats['preview_items'][] = [
                        'category' => $currentCategoryName,
                        'name' => $productName,
                        'unit' => $unit,
                        'actual_rate' => $actualRate,
                        'net_rate' => $netRate,
                        'discount_percent' => $companyDiscount,
                    ];
                }
                $stats['products_created']++;
                continue;
            }

            // Real DB insertion / update
            try {
                // Find or create Category
                $normCatName = mb_strtoupper($currentCategoryName);
                if (!isset($categoryMap[$normCatName])) {
                    $sortOrder += 10;
                    $newCat = Category::create([
                        'name' => $currentCategoryName,
                        'icon' => $this->guessCategoryIcon($currentCategoryName),
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                    ]);
                    $categoryMap[$normCatName] = $newCat;
                    $stats['categories_created']++;
                }

                $category = $categoryMap[$normCatName];

                // Check existing product
                $existingProduct = Product::where('category_id', $category->id)
                    ->where('name', $productName)
                    ->first();

                if ($existingProduct) {
                    if ($updateExisting) {
                        $existingProduct->update([
                            'unit' => $unit,
                            'actual_rate' => $actualRate,
                            'net_rate' => $netRate,
                            'discount_percent' => $companyDiscount,
                        ]);
                        $stats['products_updated']++;
                    } else {
                        $stats['products_skipped']++;
                    }
                } else {
                    Product::create([
                        'category_id' => $category->id,
                        'name' => $productName,
                        'unit' => $unit,
                        'actual_rate' => $actualRate,
                        'net_rate' => $netRate,
                        'discount_percent' => $companyDiscount,
                        'is_active' => true,
                    ]);
                    $stats['products_created']++;
                }
            } catch (\Exception $e) {
                $stats['errors'][] = "Row " . ($rowIndex + 1) . " ('{$productName}'): " . $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * Guess a suitable festive emoji icon based on category name.
     */
    protected function guessCategoryIcon(string $name): string
    {
        $nameLower = strtolower($name);

        if (str_contains($nameLower, 'sound') || str_contains($nameLower, 'bomb') || str_contains($nameLower, 'blast')) {
            return '💣';
        }
        if (str_contains($nameLower, 'pot') || str_contains($nameLower, 'flower')) {
            return '🌸';
        }
        if (str_contains($nameLower, 'chakkar') || str_contains($nameLower, 'wheel')) {
            return '🌀';
        }
        if (str_contains($nameLower, 'star') || str_contains($nameLower, 'twinkl')) {
            return '⭐';
        }
        if (str_contains($nameLower, 'rocket')) {
            return '🚀';
        }
        if (str_contains($nameLower, 'sparkler')) {
            return '✨';
        }
        if (str_contains($nameLower, 'fountain') || str_contains($nameLower, 'shower')) {
            return '⛲';
        }
        if (str_contains($nameLower, 'candle') || str_contains($nameLower, 'night')) {
            return '🕯️';
        }
        if (str_contains($nameLower, 'shot') || str_contains($nameLower, 'multi')) {
            return '🎇';
        }
        if (str_contains($nameLower, 'gift') || str_contains($nameLower, 'box') || str_contains($nameLower, 'combo')) {
            return '🎁';
        }

        return '💥';
    }
}
