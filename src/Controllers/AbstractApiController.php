<?php

namespace Yakuzan\Boiler\Controllers;

use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Yakuzan\Boiler\Entities\AbstractEntity;
use Yakuzan\Boiler\Traits\ResponseTrait;
use Yakuzan\Boiler\Traits\TransformerTrait;

abstract class AbstractApiController extends AbstractController
{
    use TransformerTrait, ResponseTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paginator = $this->service()->paginate(
            $request->input('limit', null),
            $request->input('columns', ['*']),
            $request->input('pageName', 'page'),
            $request->input('page', null)
        );


        $collection = $paginator->getCollection();

        if (0 === $collection->count()) {
            return $this->notFound();
        }

        $data = fractal($collection, $this->transformer())
            ->serializeWith(new JsonApiSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();

        return $this->respond($data);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $entity = $this->service()->find($id);

        if (null === $entity) {
            return $this->notFound();
        }

        $data = fractal($entity, $this->transformer())->toArray();

        return $this->respond($data);
    }

    public function store(Request $request)
    {
        $validator = validator($request->all(), $this->service()->entity()->access_rules($request));

        if ($validator->fails()) {
            return $this->invalidRequest(
                'The given data failed to pass validation.',
                $validator->getMessageBag()->toArray()
            );
        }

        $attributes = $request->only($this->service()->entity()->access_attributes());

        $entity = $this->service()->create($attributes);

        if ($entity instanceof AbstractEntity) {
            $data = fractal($entity, $this->transformer())->toArray();

            return $this->created(null, $data[ 'data' ]);
        }

        return $this->internalError();
    }

    public function update(Request $request, $id)
    {
        $validator = validator($request->all(), $this->service()->entity()->modify_rules($request));

        if ($validator->fails()) {
            return $this->invalidRequest(
                'The given data failed to pass validation.',
                $validator->getMessageBag()->toArray()
            );
        }

        $entity = $this->service()->find($id);

        if (null === $entity) {
            return $this->notFound();
        }

        $attributes = $request->only($this->service()->entity()->modify_attributes());

        $result = $this->service()->entity($entity)->update($attributes);

        if (true === $result) {
            return $this->accepted();
        }

        return $this->internalError();
    }

    public function destroy($id)
    {
        $entity = $this->service()->find($id);

        if (null === $entity) {
            return $this->notFound();
        }

        $result = $this->service()->entity($entity)->delete();

        if (true === $result) {
            return $this->noContent();
        }

        return $this->internalError();
    }
}
