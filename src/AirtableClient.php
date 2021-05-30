<?php

declare(strict_types=1);

namespace Yoanbernabeu\AirtableClientBundle;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * AirtableClient.
 */
class AirtableClient
{
    private string $key;
    private string $id;
    private HttpClientInterface $httpClient;
    private ObjectNormalizer $normalizer;

    public function __construct(string $key, string $id, HttpClientInterface $httpClient, ObjectNormalizer $objectNormalizer)
    {
        $this->key = $key;
        $this->id = $id;
        $this->httpClient = $httpClient;
        $this->normalizer = $objectNormalizer;
    }

    /**
     * Returns a set of rows from AirTable.
     *
     * @param string      $table     Table name
     * @param string|null $view      View name
     * @param string|null $dataClass The class name which will hold fields data
     */
    public function findAll(string $table, ?string $view = null, ?string $dataClass = null): array
    {
        $url = sprintf(
            '%s/%s%s',
            $this->id,
            $table,
            null !== $view ? '?view='.$view : ''
        );

        $response = $this->request($url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * findBy.
     *
     * Allows you to filter on a field in the table
     *
     * @param string      $table     Table name
     * @param string      $field     Search field name
     * @param string      $value     Wanted value
     * @param string|null $dataClass The class name which will hold fields data
     */
    public function findBy(string $table, string $field, string $value, ?string $dataClass = null): array
    {
        $filterByFormula = sprintf("?filterByFormula=AND({%s} = '%s')", $field, $value);
        $url = sprintf('%s/%s%s', $this->id, $table, $filterByFormula);
        $response = $this->request($url);

        return $this->mapRecordsToAirtableRecords($response->toArray()['records'], $dataClass);
    }

    /**
     * findOneById.
     *
     * @param string      $table     Table Name
     * @param string      $id        Id
     * @param string|null $dataClass The name of the class which will hold fields data
     *
     * @return array|object
     */
    public function findOneById(string $table, string $id, ?string $dataClass = null)
    {
        $url = sprintf('%s/%s/%s', $this->id, $table, $id);
        $response = $this->request($url);

        $recordData = $response->toArray();

        if (null !== $dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
        }

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * findTheLatest.
     *
     * Field allowing filtering
     *
     * @param string $table     Table name
     * @param mixed  $field
     * @param string $dataClass The name of the class which will hold fields data
     */
    public function findTheLatest(string $table, $field, ?string $dataClass = null): ?AirtableRecord
    {
        $url = $this->id.'/'
            .$table.'?pageSize=1&sort%5B0%5D%5Bfield%5D='
            .$field.'&sort%5B0%5D%5Bdirection%5D=desc';
        $response = $this->request($url);

        $recordData = $response->toArray()['records'][0] ?? null;

        if (!$recordData) {
            return null;
        }

        if (null !== $dataClass) {
            $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
        }

        return AirtableRecord::createFromRecord($recordData);
    }

    /**
     * Use the HttpClient to request Airtable API and returns the response.
     */
    private function request(string $url): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            'https://api.airtable.com/v0/'.$url,
            [
                'auth_bearer' => $this->key,
            ]
        );
    }

    /**
     * Turns an array of arrays to an array of AirtableRecord objects.
     *
     * @param array       $records   An array of arrays
     * @param string|null $dataClass Optionnal class name which will hold record's fields
     *
     * @throws ExceptionInterface
     *
     * @return array An array of AirtableRecords objects
     */
    private function mapRecordsToAirtableRecords(array $records, string $dataClass = null): array
    {
        return array_map(
            function (array $recordData) use ($dataClass): AirtableRecord {
                if (null !== $dataClass) {
                    $recordData['fields'] = $this->normalizer->denormalize($recordData['fields'], $dataClass);
                }

                return AirtableRecord::createFromRecord($recordData);
            },
            $records
        );
    }
}
