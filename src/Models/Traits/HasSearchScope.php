<?php

namespace NinjaPortal\Portal\Models\Traits;

/**
 * @property-read array $searchable
 */
trait HasSearchScope
{

    // TODO: #gyjs
    public function scopeSearch($query, $search)
    {
//        if (empty($search)) {
//            return $query;
//        }
//
//        return $query->where(function ($query) use ($search) {
//            foreach ($this->searchable as $column) {
//                if (strpos($column, '.') !== false) {
//                    $query->orWhereHas(
//                        explode('.', $column)[0],
//                        function ($query) use ($search, $column) {
//                            $query->where(explode('.', $column)[1], 'like', '%' . $search . '%');
//                        }
//                    );
//                    continue;
//                } else {
//                    $query->orWhere($column, 'like', '%' . $search . '%');
//                }
//            }
//        });
    }
}
