<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

trait TransformerTrait
{
    protected function fetch($data, $callback, $key = 'data', $render = null)
    {
        if (!$data instanceof Collection) {
            $data = $this->item($data, $callback);
        } else {
            $data = $this->collection($data, $callback, $key);
        }
        $data = $this->internalTransform($data);
        
        if ($render) {
            $data['render'] = $render;
        }

        return $data;
    }   

    protected function internalTransform($data)
    {
        $fractalManager = new Manager();
        $manager = $this->applyIncludes($fractalManager);

        if (isset($_GET['include'])) {
            $manager->parseIncludes($_GET['include']);
        }

        $manager->setSerializer(new ArraySerializer());
        $data = $manager->createData($data)->toArray();

        return array_key_exists('data', $data) ? $data['data'] : $data;
    }

    protected function applyIncludes($fractalManager)
    {
        if (isset($_GET['include'])) {
            if (count($_GET['include']) > 0) {
                $fractalManager->parseIncludes($_GET['include']);
            }
        }

        return $fractalManager;
    }
}