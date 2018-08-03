<?php
namespace App\Traits;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use App\Traits\TransformerTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait BaseAbstract
{
    use TransformerTrait;

    protected function item($item, $callback, $key = 'data')
    {
        return new Item($item, $callback, $key);
    }

    protected function collection($collection, $callback, $key = 'data')
    {
        $resource = new Collection($collection, $callback, $key);
        $paginator = new LengthAwarePaginator($collection->toArray(), $collection->count(), 10);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        
        return $resource;
    }

    public function setDataBag(array $value = [])
    {
         
        $this->dataBag = $value;

        return $this;
    }

    public function getDataBag()
    {
        return $this->dataBag;
    }
}
