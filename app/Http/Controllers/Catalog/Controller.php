<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class Controller {
    protected string $modelClass;
    protected array $attributes;
    protected string $route;
    protected array $relations = [
        'createdBy',
        'updatedBy'
    ];

    private function getModelClass(): Model
    {
        return app($this->modelClass);
    }
    
    private function extractAttributes(Request $request): array
    {
        return $request->only($this->attributes);
    }

    protected function save(Model $model, Request $request)
    {
        $attributes = $this->extractAttributes($request);

        foreach ($attributes as $attr => $val) {
            $model->setAttribute($attr, $val);
        }

        $model->save();
        $model->load($this->relations);
    }

    public function store(Request $request): RedirectResponse
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass;
        $this->save($model, $request);
        return back();
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $model = $this->getModelClass()::findOrFail($id);
        $this->save($model, $request);
        return back();
    }

    public function destroy(string $id)
    {
        $this->getModelClass()::findOrFail($id)->delete();
        return back();
    }
}