<?php

namespace Ragnarok\Svv\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Sink\Services\ChunkArchive;
use Ragnarok\Sink\Services\ChunkExtractor;
use Ragnarok\Svv\Facades\SvvFiles;
use Ragnarok\Svv\Services\SvvImporter;

class SinkSvv extends SinkBase
{
    public static $id = "svv";
    public static $title = "Statens Vegvesen";
    // Uncomment if this sink only operate on one state per chunk in DB store.
    // public $singleState = true;

    // Run fetch+import daily at 05:00
    public $cron = '0 05 * * *';

    /**
     * @inheritdoc
     */
    public function destinationTables(): array
    {
        return [
            'svv_traffic' => 'Traffic volume data from Statens Vegvesen',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        // First chunk of available data is from 2023.
        return new Carbon('2023-01-01');
    }

    /**
     * @inheritdoc
     */
    public function getToDate(): Carbon
    {
        return today()->subDay();
    }

    /**
     * @inheritdoc
     */
    public function fetch(string $id): SinkFile|null
    {
        // Retrieve data, stuff it to a single file and hand it over.
        $archive = new ChunkArchive(static::$id, $id);
        foreach (SvvFiles::getData($id) as $filename => $content) {
            $archive->addFromString($filename, $content);
        }
        return $archive->save()->getFile();
    }

    /**
     * @inheritdoc
     */
    public function import(string $chunkId, SinkFile $file): int
    {
        // Using the created archive above, import it to DB.
        $extractor = new ChunkExtractor(static::$id, $file);
        $importer = new SvvImporter($extractor->extract()->getDestDir());
        $records = 0;
        foreach ($extractor->getFiles() as $filepath) {
            $records += $importer->import($chunkId, $filepath);
        }
        return $records;
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        $importer = new SvvImporter();
        $importer->deleteImport($id);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function filenameToChunkId(string $filename): string|null
    {
        $matches = [];
        $hits = preg_match('|^(?P<date>\d{4}-\d{2}-\d{2})\.zip$|', $filename, $matches);
        return $hits ? $matches['date'] : null;
    }
}
