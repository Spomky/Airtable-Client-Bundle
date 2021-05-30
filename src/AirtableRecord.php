<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use function count;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Yoanbernabeu\AirtableClientBundle\Exception\MissingRecordDataException;

final class AirtableRecord
{
    /**
     * @var mixed
     */
    private $fields;
    private string $id;
    private DateTimeInterface $createdTime;

    /**
     * @param mixed $fields
     */
    private function __construct(string $id, $fields, DateTimeInterface $createdTime)
    {
        $this->fields = $fields;
        $this->id = $id;
        $this->createdTime = $createdTime;
    }

    /**
     * Returns an instance of AirtableRecord from values set in an array
     * Mandatory values are :
     * - id : the record id
     * - fields : the record data fields
     * - createdTime : the record created time (should be a valid datetime value).
     *
     * @param array $record The airtable record
     */
    public static function createFromRecord(array $record): self
    {
        static::ensureRecordValidation($record);

        return new self(
            $record['id'],
            $record['fields'],
            new DateTimeImmutable($record['createdTime'])
        );
    }

    /**
     * Allow anyone to ensure that a record array is valid and can be transformed to a AirtableRecord object.
     *
     * @throws MissingRecordDataException
     */
    public static function ensureRecordValidation(array $record): void
    {
        $neededFields = ['id', 'fields', 'createdTime'];
        $missingFields = [];

        foreach ($neededFields as $key) {
            if (!isset($record[$key])) {
                $missingFields[] = $key;
            }
        }

        if (count($missingFields) > 0) {
            throw new MissingRecordDataException(sprintf('Expected values missing in record array : %s', implode(', ', $missingFields)));
        }

        try {
            new DateTimeImmutable($record['createdTime']);
        } catch (Exception $e) {
            throw new MissingRecordDataException(sprintf('Value passed in the "createdTime" value is not a valid DateTime : %s', $record['createdTime']));
        }
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedTime(): DateTimeInterface
    {
        return $this->createdTime;
    }
}
