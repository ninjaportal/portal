<?php

namespace NinjaPortal\Portal\Services;

use Lordjoo\LaraApigee\Facades\LaraApigee;
use NinjaPortal\Portal\Contracts\Services\ApiProductServiceInterface;
use NinjaPortal\Portal\Utils;

class ApiProductService extends BaseService implements ApiProductServiceInterface
{

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
        return Utils::getApigeeClient()->apiProducts()->get();
    }


    public static function getModel(): string
    {
        return Utils::getApiProductModel();
    }

}
