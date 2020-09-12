<?php


namespace App\Pagination;


class PaginatedCollection
{
    private $items;
    private $total;
    private $count;
    private $links = [];

    public function __construct($items, $total)
    {
        $this->items = $items;
        $this->total = $total;
        $this->count = count($items);
    }

    public function getResult(string $collectionName): array
    {
        return [
            $collectionName => $this->items,
            'total' => $this->total,
            'count' => $this->count,
            'links' => $this->links

        ];
    }

    public function addLink($ref, $url)
    {
        $this->links[$ref] = $url;
    }
}