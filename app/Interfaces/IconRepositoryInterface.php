<?php

declare(strict_types=1);

namespace App\Interfaces;

use Illuminate\Http\Request;

interface IconRepositoryInterface
{
    public function getDefaultIcons();

    public function getRegularIconsByCategory(Request $request);

    public function getSearchIcons(Request $request);

    public function getCustomIcons(Request $request);

    public function uploadIcon(Request $request);

    public function getSingleIcon($id, $type);
}
