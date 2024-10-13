<?php

namespace NinjaPortal\Portal\Services;

use Lordjoo\LaraApigee\Facades\LaraApigee;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductService extends BaseService
{
    protected static string $model = ApiProduct::class;

    public function public()
    {
        return $this->query()->where('visibility', 'public')->get();
    }

    public function private()
    {
        return $this->query()->where('visibility', 'private')->get();
    }

    public function mine()
    {
        $user_audiences = $this->getUserAudience()->pluck('id');
        return $this->query()->where(function ($query) use ($user_audiences) {
            $query->whereHas('audiences', function ($query) use ($user_audiences) {
                $query->whereIn('audience_id', $user_audiences);
            })->orWhereDoesntHave('audiences');
        })->get();
    }

    protected function getUserAudience()
    {
        $user = auth()->user();
        return $user->audiences ?? collect();
    }

    public function apigeeProducts(): array
    {
        return LaraApigee::platform(config('ninjaportal.apigee_platform'))->apiProducts()->get();
    }


}
