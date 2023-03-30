<?php
    namespace Kairnial\LaravelApi\Models;

    use Illuminate\Http\Resources\Json\JsonResource;
    use Illuminate\Database\Eloquent\Model;
    use LogicException;

    /**
     * @method static where(string $column, string $value)
     * @method static create(array $array)
     * @method static latest()
     */
    class BaseModel extends Model
    {
        const RESOURCE_CLASS = null;

        /**
         * @param Model $model
         * @param array $relationships
         * @return JsonResource
         */
        protected static function createResource(Model $model, array $relationships) : JsonResource
        {
            $resourceClass = static::getResourceClass();

            if (is_subclass_of($resourceClass, JsonResource::class) === false)
            {
                throw new LogicException("$resourceClass is not a Resource");
            }

            /** @var JsonResource $resourceClass */
            return $resourceClass::make($model->load($relationships));
        }

        /**
         * @param array $relationships
         * @return JsonResource
         */
        protected static function createCollection(array $relationships) : JsonResource
        {
            $resourceClass = static::getResourceClass();

            if (is_subclass_of($resourceClass, JsonResource::class) === false)
            {
                throw new LogicException("$resourceClass is not a Resource");
            }
            /** @var JsonResource $resourceClass */
            return $resourceClass::collection(static::with($relationships)->get());
        }

        /**
         * @param Model $model
         * @param array $relationships
         * @return JsonResource
         */
        public static function loadRelations(Model $model, array $relationships): JsonResource
        {
            return $model->exists ? static::createResource($model, $relationships)
                                  : static::createCollection($relationships);
        }

        /**
         * @return string
         */
        protected static function getResourceClass(): string
        {
            return static::RESOURCE_CLASS;
        }
    }
